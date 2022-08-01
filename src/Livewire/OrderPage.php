<?php

namespace IdeaToCode\LaravelNovaTallPayments\Livewire;

use IdeaToCode\LaravelNovaTallPayments\Facades\Larapay;
use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use IdeaToCode\LaravelNovaTallPayments\Models\Price;
use IdeaToCode\LaravelNovaTallPayments\Models\Subscription;
use IdeaToCode\LaravelNovaTallPayments\NopManager;

class OrderPage extends Component
{
    public $products;
    public $selectedPrice;
    public $filtered;
    public $filters;
    protected $default_internal_filter = ['owner_id' => ['owner_id', '=', null], 'owner_type' => ['owner_type', '=', null]];
    public $internal_filter = [];
    public $totals = [];
    public function render()
    {
        foreach ($this->products as $product) {
            if (!is_null($product->skumodel)) {
                $app = app($product->skumodel);
                $total = ($q = $app->where(array_values($this->default_internal_filter)))->count();
                $this->totals[$product->getKey()] = $total;
            }
        }
        return view('larapay::livewire.order-page');
        // return view('livewire.order-page');
    }

    public function selectPrice($id, $reset = true)
    {
        $this->selectedPrice = $sp = Price::find($id);
        if ($reset) {
            $this->internal_filter = $this->default_internal_filter;
        }
        $this->filters = [];

        if (!is_null($sp->product->skumodel)) {
            $app = app($sp->product->skumodel);
            $this->filters = $app->getFilters();

            $this->filtered = $app->getFiltered($sp->payload->Count, array_values($this->internal_filter));

            return;
        }
        $this->filtered = null;
    }
    public function unsetFilter($key)
    {
        unset($this->internal_filter[$key]);
        $this->selectPrice($this->selectedPrice->id, false);
    }
    public function setFilter($key, $val)
    {
        $this->internal_filter[$key] = [$key, '=', $val];
        $this->selectPrice($this->selectedPrice->id, false);
    }
    public function orderProduct(Request $request)
    {

        if (!$this->selectedPrice) {
            return;
        }
        // dd($request);

        $subscriptionOwner = Larapay::getOwner($request) ?? $request->user()->currentTeam;

        // dd('orderProduct: owner', $subscriptionOwner);

        $sub = Subscription::NewSubscription($this->selectedPrice->product->model ?? NopManager::class, $subscriptionOwner, $this->selectedPrice, null);

        if (!is_null($this->selectedPrice->product->skumodel)) {

            $app = app($this->selectedPrice->product->skumodel);
            $query = $app->where(array_values($this->internal_filter))->limit($this->selectedPrice->payload->Count);

            $query->update([
                'owner_id' => $sub->getKey(),
                'owner_type' => Subscription::class,
            ]);
        }
        return redirect()->route('invoice.show', ['invoice' => $sub->invoices()->latest()->first()]);
    }
}
