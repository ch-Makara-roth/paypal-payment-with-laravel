<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Inertia\Inertia;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Illuminate\Http\Request;

class PaypalController extends Controller
{
    public function paypal(Request $request)
    {
        $data = $request->all();
        $productNames = [];
        $productQuantity = [];
        foreach ($data['products'] as $product) {
            $productNames[] = $product['name'];
            $productQuantity[] = $product['quantity'];
        }

        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $accessToken = $provider->getAccessToken();
        $order = $provider->createOrder([
            "intent" => "CAPTURE",
            "application_context" => [
                "return_url" => route('success'),
                "cancel_url" => route('cancel')
            ],
            "purchase_units" => [
                [
                    "amount" => [
                        "currency_code" => "USD",
                        "value" => $data['totalAmount']
                    ]
                ]
            ]
        ]);

        if (isset($order['links']) && $order['links'] != null) {
            foreach ($order['links'] as $link) {
                if ($link['rel'] == 'approve') {
                    session()->put('product_name', $productNames);
                    session()->put('quantity', $productQuantity);
                    return response()->json(['approve_link' => $link['href']]);
                    // return redirect()->away($link['href']);
                }
            }
        } else {
            return response()->json(['error' => 'Unable to create order'], 400);
        }
    }

    public function success()
    {
        $provider = new PayPalClient;

        $provider->setApiCredentials(config('paypal'));
        $accessToken = $provider->getAccessToken();
        $response = $provider->capturePaymentOrder(request('token'));
        // dd($response);

        if (isset($response['status']) && $response['status'] == 'COMPLETED') {
            $payment = $response['purchase_units'][0]['payments']['captures'][0];
            $paymentData = [
                'payment_id' => $payment['id'],
                'product_name' => implode(',', session()->get('product_name')),
                'quantity' => implode(',', session()->get('quantity')),
                'total_amount' => $payment['amount']['value'],
                'currency' => $payment['amount']['currency_code'],
                'payer_email' => $response['payer']['email_address'],
                'payer_name' => $response['payer']['name']['given_name'],
                'payment_status' => $payment['status'],
                'payment_method' => "Paypal",
            ];
            Payment::create($paymentData);

            return redirect()->route('dashboard')->with('success', 'Payment successful!');
        }
    }

    public function cancel()
    {
        return "Payment Cancel";
    }
}
