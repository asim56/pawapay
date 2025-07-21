<?php

namespace App\Http\Controllers;

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

    /**
     * @throws ConnectionException
     */
    public function initiatePayment(Request $request)
    {
        $request->validate([
            'phone' => 'required',
            'email' => 'required',
            'country' => 'required',
            'currency' => 'required',
            'reference_id' => 'required',
            'final_amount' => 'required',
        ]);

        $product = ProductPaymentLink::where('reference_id', $request->get('reference_id'))->first();
        $referenceId = $product->reference_id;

        $payload = [
            "depositId" => $referenceId,
            "amountDetails" => [
                "amount" => $request->get('final_amount'),
                "currency" => $request->get('currency'),
            ],
            "phoneNumber" => $request->get('phone'),
            "language" => "EN",
            "country" => $request->get('country'),
            "metadata" => [
                [
                    "customerId" => $request->get('email'),
                    "isPII" => true
                ]
            ],
            "returnUrl" => $product->redirect_url . '?ref=' . $referenceId,
        ];

        $response = Http::withToken(env('PAWAPAY_API_KEY'))
            ->withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])
            ->post(env("PAWAPAY_PAYMENT_PAGE_URL"), $payload);


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
            ['reference_id' => $referenceId],
            [
                'name' => $request->name,
                'phone' => $request->phone,
                'amount' => $request->final_amount,
                'currency' => $request->currency,
                'payment_link' => $data['redirectUrl'],
                'status' => 'pending',
            ]);

        return redirect($data['redirectUrl']);
    }


    public function paymentStatus(Request $request)
    {
        $transaction = PawapayTransaction::where('reference_id', $request->ref)->firstOrFail();
        return view('pawapay.payment.success', compact('transaction'));
    }
}
