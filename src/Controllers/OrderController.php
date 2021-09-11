<?php

namespace AlexEftimie\LaravelPayments\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use AlexEftimie\ProxyPanel\ProxyPanel;
use AlexEftimie\LaravelPayments\Models\Price;
use AlexEftimie\LaravelPayments\Models\Invoice;
use AlexEftimie\LaravelPayments\Models\Product;
use AlexEftimie\LaravelPayments\Facades\Larapay;
use AlexEftimie\LaravelPayments\Models\Subscription;
use AlexEftimie\LaravelPayments\Controllers\Controller;
use AlexEftimie\LaravelPayments\Payments\StripeGateway;
use AlexEftimie\LaravelPayments\Errors\InvoiceAlreadyPaid;

class OrderController extends Controller
{
    
    public function getProductList()
    {
        $data = [
            'products' => Product::orderBy('order','desc')->active()->get(),
        ];
        return view('larapay::order.product-list', $data);
    }

    public function postProductOrder(Request $request)
    {

        $price = Price::whereSlug($request->price_slug)->first();

        $team = $request->user()->currentTeam;

        $sub = Subscription::NewSubscription(ProxyPanel::class, $team, $price, null);

        return redirect()->route('invoice.show', [ 'invoice' => $sub->invoices()->latest()->first() ]);
    }
}