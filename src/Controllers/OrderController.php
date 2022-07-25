<?php

namespace IdeaToCode\LaravelNovaTallPayments\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
// use AlexEftimie\ProxyPanel\ProxyPanel;
use IdeaToCode\LaravelNovaTallPayments\Models\Price;
use IdeaToCode\LaravelNovaTallPayments\Models\Invoice;
use IdeaToCode\LaravelNovaTallPayments\Models\Product;
use IdeaToCode\LaravelNovaTallPayments\Facades\Larapay;
use IdeaToCode\LaravelNovaTallPayments\Models\Subscription;
use IdeaToCode\LaravelNovaTallPayments\Controllers\Controller;
use IdeaToCode\LaravelNovaTallPayments\Payments\StripeGateway;
use IdeaToCode\LaravelNovaTallPayments\Errors\InvoiceAlreadyPaid;

class OrderController extends Controller
{

    public function getProductList()
    {
        $data = [
            'products' => Product::orderBy('order', 'desc')->active()->get(),
        ];
        return view('larapay::order.product-list', $data);
    }

    public function postProductOrder(Request $request)
    {

        $price = Price::whereSlug($request->price_slug)->first();

        $team = $request->user()->currentTeam;

        $sub = Subscription::NewSubscription($price->product->model, $team, $price, null);

        return redirect()->route('invoice.show', ['invoice' => $sub->invoices()->latest()->first()]);
    }
}