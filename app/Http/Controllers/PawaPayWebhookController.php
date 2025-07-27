<?php

namespace App\Http\Controllers;

use App\Models\PawapayAccount;
use App\Models\PawapayTransaction;
use App\Models\ProductPaymentLink;
use App\Models\Webhook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PawaPayWebhookController extends Controller
{
    public function index(Request $request)
    {
        Webhook::created([
            "payload" => $request->all(),
        ]);
        $cancel = $request->get('cancel', null);
        $ref = $request->get('ref', null);
        if (!empty($cancel)) {
            return redirect(url("/product/payment/" . $ref));
        }

        $status = null;
        $depositId = $request->get('depositId', null);
        if ($depositId) {


            $link = ProductPaymentLink::where(["reference_id" => $ref])->first();
            $pawapayAccount = PawapayAccount::find($link->id);

            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $pawapayAccount->api_key,
            ])->get(env("PAWAPAY_PAYMENT_PAGE_URL") . '/deposits/e977443b-1a90-493d-ba2f-451fc3d8ea48');


            if ($response->successful()) {
                $data = ($response->json()); // or ->body() for raw
                $response = $data["data"] ?? null;
                $status = $response["status"] ?? null;
                PawapayTransaction::where(["deposit_id" => $depositId])->update([
                    "status"=>$response["status"] ?? null,
                    "webhook" => json_encode($request->all())]
                );
                Http::post('https://connect.pabbly.com/workflow/sendwebhookdata/IjU3NjYwNTZhMDYzNTA0MzA1MjZhNTUzMzUxMzAi_pc', json_encode($response));
            }
        }


        return response()->json(['message' => 'Webhook received']);
    }
}
