<?php

namespace IdeaToCode\LaravelNovaTallPaymentsayments\Livewire;

use Livewire\Component;
use Illuminate\Http\Request;
use IdeaToCode\LaravelNovaTallPaymentsayments\Models\Invoice;
use IdeaToCode\LaravelNovaTallPaymentsayments\Facades\Larapay;
use IdeaToCode\LaravelNovaTallPaymentsayments\Models\Subscription;

class TeamBillingManager extends Component
{
    public $invoiceBeingPaid = null;
    public $team;
    public $choosePaymentMethodOpen = false;
    public $gatewayModalOpen = false;
    public $currentGateway = null;
    public $showPaymentConfirmationModal = false;

    protected $listeners = ['paymentMethodUpdated'];

    public function render()
    {
        return view('larapay::livewire.team-billing-manager');
    }

    public function payInvoice(Request $request, $gateway)
    {
        $this->currentGateway = $gateway;
        $this->gatewayModalOpen = true;
        $this->choosePaymentMethodOpen = false;

        $this->dispatchBrowserEvent('loadGateway', ['gateway' => $gateway]);

        // return Larapay::showGatewayForm($request, $gateway, $this->invoiceBeingPaid)->render();
    }


    public function choosePaymentMethod(Invoice $invoice)
    {
        $this->choosePaymentMethodOpen = true;
        $this->invoiceBeingPaid = $invoice;
    }
    public function paymentMethodUpdated(Request $request, $token)
    {

        $result = Larapay::paymentMethodUpdated($request, $this->currentGateway, $this->invoiceBeingPaid, $token);

        if ($result) {
            $this->showPaymentConfirmationModal = true;
            $this->gatewayModalOpen = false;
            $this->choosePaymentMethodOpen = false;
            $this->invoiceBeingPaid = null;
            $this->currentGateway = null;
        }
    }
    public function manageSubscription(Subscription $sub)
    {

        $route = Larapay::getManagementRoute($sub);

        return redirect()->route($route, $sub);
    }
}