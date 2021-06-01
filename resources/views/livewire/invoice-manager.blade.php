<div x-data="{status: @entangle('status')}">

    <div class="flex justify-between">
        @if (Gate::check('refund', $invoice))
            <x-jet-danger-button x-show="status == 'paid'" wire:click="confirmRefund(true)">{{ __('Refund') }}</x-jet-danger-button>
        @endif
        @if(!$invoice->subscription->isOff())
            <x-jet-button x-show="status != 'paid'" wire:click="$set('isOpen', true)">{{ __('Pay') }}</x-jet-button>
        @endif
        {{-- <x-jet-button x-show="status == 'paid'" wire:click="download">{{ __('Download') }}</x-jet-button> --}}
    </div>

    <!-- Gateway Modal -->
    <x-jet-dialog-modal wire:model="isOpen">
        <x-slot name="title">
            {{ __('Choose Payment Method') }}:{{ $isOpen ? 'ye' : 'no'}}
        </x-slot>

        <x-slot name="content">
            <div class="relative z-0 mt-1 border border-gray-200 rounded-lg cursor-pointer">
                <?php $index = 0; ?>
                @foreach ( Larapay::gateways() as $slug => $opts )
                    {{-- <a href="{{route('larapay.gateway', [ 'gateway' => $slug, 'invoice' => $invoice ])}}"> --}}
                        <button wire:click="openGateway('{{$slug}}')" class="relative px-4 py-3 text-left inline-flex w-full rounded-lg focus:z-10 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue {{ $index > 0 ? 'border-t border-gray-200 rounded-t-none' : '' }} {{ ! $loop->last ? 'rounded-b-none' : '' }}">
                            <div class="w-1/6">
                                {!! $opts['src'] !!}
                            </div>
                            <div class="w-5/6">

                                <!-- Gateway Name -->
                                <div class="text-sm text-gray-600">
                                    {{ ucwords($opts['name']) }}
                                </div>
                                

                                <!-- Gateway Description -->
                                <div class="mt-2 text-xs text-gray-600">
                                    {!! $opts['description'] ?? '' !!}
                                </div>
                            </div>
                        </button>
                    <?php $index++; ?>
                @endforeach
            </div>
        </x-slot>
        <x-slot name="footer">
            <div class="flex items-start">
                <x-jet-secondary-button wire:click="$set('isOpen', false)" wire:loading.attr="disabled">
                    {{ __('Nevermind') }}
                </x-jet-secondary-button>
            </div>
        </x-slot>
    </x-jet-dialog-modal>
    @if(!is_null($currentGateway))
        <?php list($view, $data) = Larapay::getPaymentModalView($currentGateway); ?>
        @include($view, $data)
    @endif
    <!-- Gateway Modal -->
    <x-jet-dialog-modal wire:model="isConfirmRefundOpen">
        <x-slot name="title">
            {{ __('Refund')}}
        </x-slot>

        <x-slot name="content">
            Are you sure you want to refund this invoice?
        </x-slot>
        <x-slot name="footer">
            <div class="flex justify-between">
                <x-jet-danger-button wire:click="refund" wire:loading.attr="disabled">
                    {{ __('Refund') }}
                </x-jet-danger-button>
                <x-jet-secondary-button wire:click="$set('isConfirmRefundOpen', false)" wire:loading.attr="disabled">
                    {{ __('Nevermind') }}
                </x-jet-secondary-button>
            </div>
        </x-slot>
    </x-jet-dialog-modal>
</div>