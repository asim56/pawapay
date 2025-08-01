<?php

namespace App\Http\Controllers;

use App\Models\PawapayAccount;
use App\Models\PawapayTransaction;
use App\Models\ProductPaymentLink;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class PawaPayController extends Controller
{
    public function index()
    {
        return view('pawapay.payment.form');
    }


    function productPayment($token)
    {
        $product = ProductPaymentLink::where('reference_id', $token)->first();

        if (!$product) {
            abort(404, 'Product details not found');
        }

        $countries = $this->getCountriesList();

        return view('pawapay.product.payment.currency_converter', compact('product', 'countries'));

    }

    /**
     * @throws ConnectionException
     */
    public function initiatePayment(Request $request)
    {
        $request->validate([
            'phone' => 'required',
            'country_code' => 'required',
            'email' => 'required',
            'country' => 'required',
            'currency' => 'required',
            'reference_id' => 'required',
            'final_amount' => 'required',
        ]);

        $product = ProductPaymentLink::where('reference_id', $request->get('reference_id'))->first();
        $referenceId = $product->reference_id;

        $country = $request->get('country');
        if ($country == "USD") {
            $country = "COD";
        }

        $phone = $this->formatPhoneNumber($request->get('phone'), $request->get('country_code'));
        $depositId = (string)Str::uuid();
        $payload = [
            "depositId" => $depositId,
            "amountDetails" => [
                "amount" => $request->get('final_amount'),
                "currency" => $request->get('currency'),
            ],
            "phoneNumber" => $phone,
            "language" => "EN",
            "country" => $country,
            "metadata" => [
                [
                    "customerId" => $request->get('email'),
                    "isPII" => true
                ],
                [
                    "product_link_id" => $product->reference_id
                ],
            ],
            "returnUrl" => $product->redirect_url . '?ref=' . $referenceId,
        ];

        $pawapayAccount = PawapayAccount::find($product->pawapay_account_id);
        if (!$pawapayAccount || !isset($pawapayAccount->api_key)) {
            return back()->with('error', "Payapay account credentials are not set");
        }

        $gatewayURL = env("PAWAPAY_PAYMENT_PAGE_URL");
        if (!$pawapayAccount->is_live_account) {
            $gatewayURL = env("PAWAPAY_PAYMENT_SANDBOX_PAGE_URL");
        }

        $response = Http::withToken($pawapayAccount->api_key)
            ->withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])
            ->post($gatewayURL . "/paymentpage", $payload);


        if ($response->failed()) {
            $errorBody = $response->json(); // Decoded JSON response (as array)

            $errorMessage = isset($errorBody['failureReason']['failureCode']) ? $errorBody['failureReason']['failureCode'] . ":" : "";
            $errorMessage .= $errorBody['failureReason']['failureMessage'] ?? "";
            return back()->with('error', $errorMessage ?? 'Failed to initiate payment.');
        }

        $data = $response->json();
        if (isset($data['failureReason']['failureCode'])) {
            $errorMessage = isset($data['failureReason']['failureCode']) ? $data['failureReason']['failureCode'] . ":" : "";
            $errorMessage .= $data['failureReason']['failureMessage'] ?? "Failed to initiate payment.";
            return back()->with('error', $errorMessage);
        }

        PawapayTransaction::updateOrCreate(
            ['deposit_id' => $depositId],
            [
                'product_link_id' => $product->reference_id,
                'name' => $request->name,
                'phone' => $request->phone,
                'amount' => $request->final_amount,
                'currency' => $request->currency,
                'payment_link' => $data['redirectUrl'],
                'status' => 'pending',
            ]);

        return redirect($data['redirectUrl']);
    }

    function formatPhoneNumber($phone, $countryCode)
    {
        // Remove any non-digit characters for comparison (optional)
        $normalizedPhone = preg_replace('/\D/', '', $phone);
        $normalizedCode = preg_replace('/\D/', '', $countryCode);

        // Check if phone starts with country code
        if (strpos($normalizedPhone, $normalizedCode) === 0) {
            return $normalizedPhone;
        }

        // Prepend country code
        return $normalizedCode . $normalizedPhone;
    }

    public function paymentStatus(Request $request)
    {
        $cancel = $request->get('cancel', null);
        $ref = $request->get('ref', null);
        if (!empty($cancel) && isset($ref)) {
            return redirect(url("/product/payment/" . $ref));
        }

        $status = null;
        $depositId = $request->get('depositId', null);
        if ($depositId) {


            $link = ProductPaymentLink::where(["reference_id" => $ref])->first();
            $pawapayAccount = PawapayAccount::find($link->pawapay_account_id);

            $gatewayURL = env("PAWAPAY_PAYMENT_PAGE_URL");
            if (!$pawapayAccount->is_live_account) {
                $gatewayURL = env("PAWAPAY_PAYMENT_SANDBOX_PAGE_URL");
            }

            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $pawapayAccount->api_key,
            ])->get($gatewayURL . '/deposits/e977443b-1a90-493d-ba2f-451fc3d8ea48');


            if ($response->successful()) {
                $data = ($response->json()); // or ->body() for raw
                $response = $data["data"] ?? null;
                $status = $response["status"] ?? null;
                $rawResponse["pawapay"] = $request->all();

                try {
                    $rawResponse["pabbly"] = Http::get('https://connect.pabbly.com/workflow/sendwebhookdata/IjU3NjYwNTZhMDYzNTA0MzA1MjZhNTUzMzUxMzAi_pc', json_encode($response));
                } catch (\Exception $exception) {
                    $rawResponse["exception"] = $exception->getMessage();
                }
                PawapayTransaction::where(["deposit_id" => $depositId])->update([
                        "status" => $response["status"] ?? null,
                        "webhook" => json_encode($rawResponse)
                    ]
                );
            }
        }

        return view('pawapay.payment.success', compact('depositId', 'status'));
    }

    function getCountriesList()
    {
        return [
            [
                'name' => 'Benin',
                'alpha2' => 'BJ',
                'alpha3' => 'BEN',
                'dial_code' => '+229',
                'currency' => 'XOF',
            ],
            [
                'name' => 'Burkina Faso',
                'alpha2' => 'BF',
                'alpha3' => 'BFA',
                'dial_code' => '+226',
                'currency' => 'XOF',
            ],
            [
                'name' => 'Cameroon',
                'alpha2' => 'CM',
                'alpha3' => 'CMR',
                'dial_code' => '+237',
                'currency' => 'XAF',
            ],
            [
                'name' => 'Côte d’Ivoire',
                'alpha2' => 'CI',
                'alpha3' => 'CIV',
                'dial_code' => '+225',
                'currency' => 'XOF',
            ],
            [
                'name' => 'Democratic Republic of the Congo (CDF)',
                'alpha2' => 'CD',
                'alpha3' => 'COD',
                'dial_code' => '+243',
                'currency' => 'CDF',
            ],
            [
                'name' => 'Democratic Republic of the Congo (USD)',
                'alpha2' => 'CD',
                'alpha3' => 'COD',
                'dial_code' => '+243',
                'currency' => 'USD',
            ],
            [
                'name' => 'Gabon',
                'alpha2' => 'GA',
                'alpha3' => 'GAB',
                'dial_code' => '+241',
                'currency' => 'XAF',
            ],
            [
                'name' => 'Ghana',
                'alpha2' => 'GH',
                'alpha3' => 'GHA',
                'dial_code' => '+233',
                'currency' => 'GHS',
            ],
            [
                'name' => 'Kenya',
                'alpha2' => 'KE',
                'alpha3' => 'KEN',
                'dial_code' => '+254',
                'currency' => 'KES',
            ],
            [
                'name' => 'Malawi',
                'alpha2' => 'MW',
                'alpha3' => 'MWI',
                'dial_code' => '+265',
                'currency' => 'MWK',
            ],
            [
                'name' => 'Mozambique',
                'alpha2' => 'MZ',
                'alpha3' => 'MOZ',
                'dial_code' => '+258',
                'currency' => 'MZN',
            ],
            [
                'name' => 'Nigeria',
                'alpha2' => 'NG',
                'alpha3' => 'NGA',
                'dial_code' => '+234',
                'currency' => 'NGN',
            ],
            [
                'name' => 'Republic of the Congo',
                'alpha2' => 'CG',
                'alpha3' => 'COG',
                'dial_code' => '+242',
                'currency' => 'XAF',
            ],
            [
                'name' => 'Rwanda',
                'alpha2' => 'RW',
                'alpha3' => 'RWA',
                'dial_code' => '+250',
                'currency' => 'RWF',
            ],
            [
                'name' => 'Senegal',
                'alpha2' => 'SN',
                'alpha3' => 'SEN',
                'dial_code' => '+221',
                'currency' => 'XOF',
            ],
            [
                'name' => 'Sierra Leone',
                'alpha2' => 'SL',
                'alpha3' => 'SLE',
                'dial_code' => '+232',
                'currency' => 'SLE',
            ],
            [
                'name' => 'Tanzania',
                'alpha2' => 'TZ',
                'alpha3' => 'TZA',
                'dial_code' => '+255',
                'currency' => 'TZS',
            ],
            [
                'name' => 'Uganda',
                'alpha2' => 'UG',
                'alpha3' => 'UGA',
                'dial_code' => '+256',
                'currency' => 'UGX',
            ],
            [
                'name' => 'Zambia',
                'alpha2' => 'ZM',
                'alpha3' => 'ZMB',
                'dial_code' => '+260',
                'currency' => 'ZMW',
            ],
        ];
    }
}
