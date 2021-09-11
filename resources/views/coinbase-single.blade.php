<div>
	<!--  Coinbase Single Payment Modal -->
	<x-jet-dialog-modal wire:model="isGatewayOpen">
		<x-slot name="title">
			{{ __('Pay Using Crypto') }}
		</x-slot>

		<x-slot name="content">
			{{ __('By clicking the Pay button you will be taken to Coinbase.com where you can pay using your desired Crypto Coin')}}
		</x-slot>

		<x-slot name="footer">
			<div class="flex justify-between">
				<x-jet-secondary-button wire:click="hideGateway" wire:loading.attr="disabled">
						{{ __('Nevermind') }}
				</x-jet-secondary-button>
				<x-jet-button type="button" wire:click="createSingleCharge" wire:loading.attr="disabled">
					{{ __('Pay') }}
				</x-jet-button>
			</div>
		</x-slot>
	</x-jet-dialog-modal>
</div>
