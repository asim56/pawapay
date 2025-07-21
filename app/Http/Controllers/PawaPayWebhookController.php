<?php

namespace App\Http\Controllers;

use App\Models\PawapayTransaction;
use Illuminate\Http\Request;

class PawaPayWebhookController extends Controller
{
    public function index(Request $request)
    {
        $payload = $request->all();

        $transaction = PawapayTransaction::where('reference_id', $payload['reference'])->first();
        if ($transaction) {
            $transaction->status = strtolower($payload['status']); // e.g., 'paid', 'failed'
            $transaction->webhook = json_encode($payload);
            $transaction->save();
        } else {
            $transaction = PawapayTransaction::first();
            if ($transaction) {
                $transaction->webhook = json_encode($payload);
                $transaction->save();
            }
        }

        return response()->json(['message' => 'Webhook received']);
    }
}
