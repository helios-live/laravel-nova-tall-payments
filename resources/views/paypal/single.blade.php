<div>
<script>

//   const paypalLoadedEvent = new Event('paypalLoaded');
//   window.addEventListener('loadGateway', event => {
//     if (window.hasPaypal){ 
//       document.dispatchEvent(paypalLoadedEvent);
//     } else {

// {{--      window.Livewire.emit('paymentMethodUpdated', {
//         "invoice": '{{$invoiceBeingPaid->uuid}}'
//       })
// --}}
//       window.Livewire.on('payPaypalSingle', event => {
//         // window.createStripeToken();
//       });
//       // var scr = document.createElement('script')
//       // scr.src = "https://js.stripe.com/v3/"
//       // scr.type = "text/javascript"
//       // scr.onload = function(){
//       //   console.log("Loaded Stripe")
//       //   document.dispatchEvent(paypalLoadedEvent);
//       //   window.hasPaypal = true
//       // }
//       // document.body.appendChild(scr);
//     }
//   });
</script>
<!--  Stripe Single Payment Modal -->
<x-jet-dialog-modal wire:model="isGatewayOpen">
    <x-slot name="title">
        {{ __('PayPal Single Charge') }}
    </x-slot>

    <x-slot name="content">
        {{ __('Click Take me to PayPal to pay this invoice using a single charge') }}
    </x-slot>


    <x-slot name="footer">
        <div class="flex justify-between">
            <x-jet-secondary-button wire:click="hideGateway">
                {{ __('Nevermind') }}   
            </x-jet-secondary-button>
            <x-jet-button wire:click="createSingleCharge" wire:loading.attr="disabled">
                {{ __('Take me to PayPal') }}
            </x-jet-button>
        </div>
    </x-slot>
</x-jet-dialog-modal>
</div>
