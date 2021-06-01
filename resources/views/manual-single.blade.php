<div>
<!--  Stripe Single Payment Modal -->
<x-jet-dialog-modal wire:model="isGatewayOpen">
    <x-slot name="title">
        {{ __('Manually Paying as Admin') }}
    </x-slot>

    <x-slot name="content">
      {{ __('Are you sure you want to manually pay this invoice?') }}
    </x-slot>


    <x-slot name="footer">
        <div class="flex justify-between">
            <x-jet-secondary-button wire:click="hideGateway">
                {{ __('Nevermind') }}
            </x-jet-secondary-button>
            <x-jet-button wire:click="charge" wire:loading.attr="disabled">
                {{ __('Pay') }}
            </x-jet-button>
        </div>
    </x-slot>
</x-jet-dialog-modal>
</div>
