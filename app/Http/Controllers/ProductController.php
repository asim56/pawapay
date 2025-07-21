<?php

namespace App\Http\Controllers;

use App\Models\PawapayTransaction;
use App\Models\ProductPaymentLink;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function showPaymentLinkCreator()
    {
        return view('pawapay.product.payment.link_creator');
    }

    public function submitPaymentLinkCreator(Request $request)
    {
        $request->validate([
            'name' => 'required|min:4',
            'price' => 'required',
            'redirect_url' => 'required',
        ]);

        $referenceId = (string) Str::uuid();
        ProductPaymentLink::create([
            'reference_id' => $referenceId,
            'name' => $request->name,
            'price' => $request->price,
            'redirect_url' => $request->redirect_url,
            'status' => 'pending',
        ]);

        return response()->json(["status"=> "success", "url"=> url("product/payment/".$referenceId)]);
    }

    function productPayment($token)
    {
        $product = ProductPaymentLink::where('reference_id', $token)->first();

        if (!$product) {
            abort(404, 'Product details not found');
        }

        return view('pawapay.product.payment.currency_converter', compact('product'));

    }

    public function paymentStatus(Request $request)
    {
        $transaction = PawapayTransaction::where('reference_id', $request->ref)->firstOrFail();
        return view('pawapay.payment.success', compact('transaction'));
    }
}
