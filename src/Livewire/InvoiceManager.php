<?php

namespace AlexEftimie\LaravelPayments\Livewire;

use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use AlexEftimie\LaravelPayments\Models\Log;
use AlexEftimie\LaravelPayments\Models\Invoice;
use AlexEftimie\LaravelPayments\Facades\Larapay;
use AlexEftimie\LaravelPayments\Models\Subscription;

class InvoiceManager extends Component
{

    // Various Modals
    public $isOpen = false;
    public $isGatewayOpen = false;
    public $isConfirmRefundOpen = false;


    public $invoice;
    public $status;
    public $currentGateway;
    public $payload = null;

    protected $listeners = [
        'createCustomer' => 'createCustomer',
        'createSingleCharge' => 'createSingleCharge',
        // 'updatePaymentMethods' => 'updatePaymentMethods',
        'charge' => 'charge',
        'setPayload' => 'setPayload',
        'openGateway' => 'openGateway',
    ];

    public function render()
    {
        return view('larapay::livewire.invoice-manager');
    }
    
    public function openGateway(Request $request, $gateway = null)
    {
        if ( !is_null($gateway) ) {
            $this->currentGateway = $gateway;
        }
        $this->isGatewayOpen = true;
        $this->isOpen = false;

        $this->dispatchBrowserEvent('openGateway', ['gateway' => $gateway]);
    }

    public function setPayload($payload)
    {
        $this->payload = $payload;
        $this->dispatchBrowserEvent('payloadUpdated', $this->payload);
    }

    public function createCustomer()
    {
        return $this->parseResponse(Larapay::createCustomer($this->currentGateway, $this->invoice, $this->payload));
    }

    public function updatePaymentMethods($param)
    {
        return $this->parseResponse(Larapay::updatePaymentMethods($param, $this->currentGateway, $this->invoice, $this->payload));
    }

    public function createSingleCharge()
    {
        return $this->parseResponse(Larapay::createSingleCharge($this->currentGateway, $this->invoice, $this->payload));
    }

    public function charge() {

        return $this->parseResponse(Larapay::charge($this->currentGateway, $this->invoice, $this->payload));
    }

    public function refund() {

        if (!Gate::check('refund', $this->invoice)) {
            $result = (object)[
                'status' => 'failed'
            ];
            Log::add($this->invoice->owner, $this->currentGateway . '::refund::failed', ['user' => auth()->user()]);
        } else {
            $result = Larapay::refund('admin', $this->invoice);
        }
        return $this->parseResponse($result);
    }

    public function parseResponse($response)
    {
        if (isset($response->status) && $response->status == 'setPayload' )
        {
            $this->setPayload( $response->payload );
            return;
        } elseif ( isset($response->status) && $response->status =='emit' ) {
            call_user_func_array([$this, 'emit'], array_merge([$response->event], $response->arguments));
            return;
        }
        return $response;
    }

    public function confirmRefund($show = true)
    {
        $this->isConfirmRefundOpen = $show;
    }

    public function hideGateway()
    {
        $this->isGatewayOpen = false;
        $this->isOpen = true;
    }
}
