<div>
<script>

	/**
	 * Step 1. Process Event openGateway
	 * Step 2. Load stripe elements if not present
	 * Step 3. Emit Event openStripeEvent
	 * Step 4. Process Event openStripeEvent
	 * Step 5. Setup the Stripe Elements
	 * Step 6. On Form submit: Create a Stripe payment Token
	 * Step 7. Send Token to the Backend
	 * Step 8. Backend creates the order
	 */

	const openStripeEvent = new Event('openStripeEvent');

	window.hasStripeMethod = {{ is_null($invoice->owner->stripe_pm_id) ? "false" : "true" }};
	window.paymentMethod = '{{ $invoice->owner->stripe_pm_id }}';

	window.addEventListener('payloadUpdated', function(e){
		if ( typeof e.detail !== 'object' ) {
			return
		}
		let data = e.detail;
		if ( data.action == "use_stripe_sdk" ) {
			window.stripeClient.confirmCardPayment(data.client_secret).then(function(response) {
				console.log("Stripe confirmCardPayment", response)
				if (response.error) {
					// Handle error here
				} else if (response.paymentIntent && response.paymentIntent.status === 'succeeded') {
					// Handle successful payment here
					window.Livewire.emit('charge')
				}
			})	
		}
	});

	window.addEventListener('openGateway', event => {
		console.log('openGateway', event)


		// if Stripe is already loaded, just dispatch the event
		if (window.hasStripe)
		{
			window.dispatchEvent(openStripeEvent);
		}
		else
		{
			console.log('Loaded hooks')

			// load the Stripe Elements
			var scr = document.createElement('script')
			scr.src = "https://js.stripe.com/v3/"
			scr.type = "text/javascript"
			scr.onload = function(){
				console.log("Loaded Stripe Elements")
				window.dispatchEvent(openStripeEvent);
				window.hasStripe = true
			}
			document.body.appendChild(scr);
			}
	});


	window.addEventListener('openStripeEvent', function(event) {
		console.log('Loading Stripe Elements', event);

		// Init the Stripe Elements
		window.stripeClient = Stripe('{{$STRIPE_KEY}}');

		// if we already have a payment method, no need to load the form

		let el = document.getElementById('card-element')
		if (!el) {
			return
		}

		var elements = window.stripeClient.elements();

		window.stripeCard = elements.create('card');

		// Add an instance of the card UI component into the `card-element` <div>
		window.stripeCard.mount('#card-element');

		// Handle events and errors
		window.stripeCard.addEventListener('change', function(event) {
			var displayError = document.getElementById('card-errors');
			if (event.error)
			{
				displayError.textContent = event.error.message;
			}
			else
			{
				displayError.textContent = '';
			}
		});

	});

	window.createStripeToken = function () {

		let el = document.getElementById('card-element')
		if ( !el ) {
			window.Livewire.emit('createSingleCharge')
			// window.Livewire.emit('charge')
			window.chargeMode = 'withSavedCard'
			return;
		}

		// Create a Stripe Token
		window.stripeClient.createToken(window.stripeCard).then(function(result) {
			if (result.error)
			{
				// Inform the user if there was an error
				var errorElement = document.getElementById('card-errors');
				errorElement.textContent = result.error.message;
			}
			else
			{
				window.Livewire.emit('createCustomer')
				window.Livewire.emit('setPayload', result.token.id)
				window.Livewire.emit('createSingleCharge')
				window.stripeClient.confirmCardPayment
			}
		});
	};

</script>


<!--  Stripe Single Payment Modal -->
<x-jet-dialog-modal wire:model="isGatewayOpen">
		<x-slot name="title">
				{{ __('Pay Using Credit or Debit Card') }}
		</x-slot>

		<x-slot name="content">
			@if($invoice->owner->stripe_pm_id)
				<div class="flex justify-between border rounded-xl mt-5 shadow-md items-center">
					<div class="flex items-center h-20 pl-3">
						<div class="w-20">
							{!! AlexEftimie\LaravelPayments\Payments\StripeGateway::getCard($invoice->owner->stripe_card_data->brand) !!}
						</div>
						<div class="font-bold">
							{{ $invoice->owner->stripe_card_data->last4 }}
						</div>
					</div>
					<div class="p-5">
						<x-jet-danger-button type="button" wire:click="updatePaymentMethods({ 'action': 'deleteCard' })" wire:loading.attr="disabled">X</x-jet-danger-button>
						<x-jet-button type="button" x-on:click="createStripeToken()" wire:loading.attr="disabled">
							{{ __('Use Card') }}
						</x-jet-button>
					</div>
				</div>
			@else
				<form id="payment-form" dusk="gateway-stripe" x-on:submit="">
					@csrf
					<div id="card-element" class="mt-10 mb-2 h-11 border border-gray-200 w-full flex-1 text-sm bg-grey-light text-grey-darkest rounded-md p-3 focus:outline-none"></div>                  
					<div class="text-red-600 mb-8" id="card-errors" role="alert">&nbsp;</div>
				</form>
			@endif
		</x-slot>


		<x-slot name="footer">
			<div class="flex justify-between">
				<x-jet-secondary-button wire:click="hideGateway" wire:loading.attr="disabled">
						{{ __('Nevermind') }}
				</x-jet-secondary-button>
				@if( is_null($invoice->owner->stripe_pm_id) )
					<x-jet-button type="button" x-on:click="createStripeToken()" wire:loading.attr="disabled">
							{{ __('Pay') }}
					</x-jet-button>
				@endif
			</div>
		</x-slot>
</x-jet-dialog-modal>
</div>
