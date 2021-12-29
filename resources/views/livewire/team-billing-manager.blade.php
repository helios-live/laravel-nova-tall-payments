<div>
    <x-jet-section-border />

    <!-- Add Team Member -->
    <div class="mt-10 sm:mt-0">
            <x-jet-action-section>
                <x-slot name="title">
                    {{ __('Subscriptions') }}
                </x-slot>

                <x-slot name="description">
                    {{ __('Billing section for this team.') }}
                </x-slot>

                <!-- Team Member List -->
                <x-slot name="content">
                    <div class="space-y-6">
            			<x-jet-label value="{{ __('Team Subscriptions') }}" />
                            <div class="relative z-0 mt-1 border border-gray-200 rounded-lg cursor-pointer">
			                @foreach ($team->subscriptions()->orderBy('id', 'desc')->get() as $index => $sub)
                                <div wire:key="sub-{{$sub->id}}" class="relative px-4 py-3 inline-flex w-full rounded-lg focus:z-10 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue {{ $index > 0 ? 'border-t border-gray-200 rounded-t-none' : '' }} {{ ! $loop->last ? 'rounded-b-none' : '' }}"
                                                {{-- wire:click="$set('addTeamMemberForm.role', '{{ false }}')" --}}
                                                >
                                    <div class="w-1/2 {{ $sub->status == 'Ended' ? 'opacity-50' : '' }}">
                                        <!-- Role Name -->
                                        <div class="flex items-center">
                                            <div class="text-sm text-gray-600">
                                                {{ $sub->name }}
                                            </div>

                                            @if ( $sub->status == 'Active' )
                                                <svg class="ml-2 h-5 w-5 text-green-400" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            @endif
                                        </div>

                                        <!-- Role Description -->
                                        <div class="mt-2 text-xs text-gray-600">
                                            {{Larapay::formatPrice($sub->current_price)}} / {{Larapay::formatPeriod($sub)}} /
                                            {{ $sub->status }}
                                        </div>
                                    </div>
                                    <div class="w-1/2 text-right pt-1">
                                            @if ( $sub->status == 'Active' )

                                                <x-jet-button class="ml-2" wire:click="manageSubscription('{{$sub->id}}')" wire:loading.attr="disabled">
                                                    {{ __('Manage') }}
                                                </x-jet-button>
                                            @endif
                                    </div>
                                </div>
			                @endforeach
			            </div>
                    </div>
                </x-slot>
            </x-jet-action-section>
    </div>


        <x-jet-section-border />

        <!-- Manage Team Members -->
        <div class="mt-10 sm:mt-0" id="billing">
            <x-jet-action-section>
                <x-slot name="title">
                    {{ __('Team Billing') }}
                </x-slot>

                <x-slot name="description">
                    {{ __('Billing section for this team.') }}
                </x-slot>

                <!-- Team Member List -->
                <x-slot name="content">
                    <div class="space-y-6">
            			<x-jet-label value="{{ __('Team Invoices') }}" />
                            <div class="relative z-0 mt-1 border border-gray-200 rounded-lg ">
			                @foreach ($team->invoices as $index => $invoice)
                                <div class="cursor-pointer relative px-4 py-3 inline-flex w-full rounded-lg focus:z-10 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue {{ $index > 0 ? 'border-t border-gray-200 rounded-t-none' : '' }} {{ ! $loop->last ? 'rounded-b-none' : '' }}"
                                                {{-- wire:click="$set('addTeamMemberForm.role', '{{ false }}')" --}}
                                                >
                                    <a href="{{ route('invoice.show', ['invoice' => $invoice]) }}"  class="w-1/2 {{ $invoice->is_paid? 'opacity-50' : '' }}">
                                        <!-- Role Name -->
                                        <div class="flex items-center">
                                            <div class="text-sm text-gray-600">
                                                {{ $invoice->name }}
                                            </div>

                                            @if (!is_null($invoice->paid_at))
                                                <svg class="ml-2 h-5 w-5 text-green-400" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            @endif
                                        </div>

                                        <!-- Role Description -->
                                        <div class="mt-2 text-xs text-gray-600">
                                            {{Larapay::formatPrice($invoice->amount)}} / {{ $invoice->uuid }}
                                        </div>
                                    </a>
                                    <div class="w-1/2">
                                        @livewire('larapay::invoice-manager', ['invoice' => $invoice, 'status' => $invoice->status])
                                    </div>
                                </div>
			                @endforeach
			            </div>
                    </div>
                </x-slot>
            </x-jet-action-section>
        </div>

    <!-- Role Management Modal -->
    <x-jet-dialog-modal wire:model="choosePaymentMethodOpen">
        <x-slot name="title">
            {{ __('Choose Payment Method') }}
        </x-slot>

        <x-slot name="content">
            <div class="relative z-0 mt-1 border border-gray-200 rounded-lg cursor-pointer">
                <?php $index = 0; ?>
                @foreach ( Larapay::gateways() as $slug => $opts )
                    {{-- <a href="{{route('larapay.gateway', [ 'gateway' => $slug, 'invoice' => $invoice ])}}"> --}}
                        <button wire:click="payInvoice('{{$slug}}')" class="relative px-4 py-3 text-left inline-flex w-full rounded-lg focus:z-10 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue {{ $index > 0 ? 'border-t border-gray-200 rounded-t-none' : '' }} {{ ! $loop->last ? 'rounded-b-none' : '' }}">
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
            <x-jet-secondary-button wire:click="$toggle('choosePaymentMethodOpen')" wire:loading.attr="disabled">
                {{ __('Nevermind') }}
            </x-jet-secondary-button>
        </x-slot>

    </x-jet-dialog-modal>

    <x-jet-dialog-modal wire:model="showPaymentConfirmationModal">
        <x-slot name="title">
            {{ __('Payment Successful') }}
        </x-slot>

        <x-slot name="content">
            Your payment was successful.
        </x-slot>

        <x-slot name="footer">
            <x-jet-secondary-button wire:click="$toggle('showPaymentConfirmationModal')" wire:loading.attr="disabled">
                {{ __('Close') }}
            </x-jet-secondary-button>

        </x-slot>
    </x-jet-dialog-modal>
    @if(!is_null($currentGateway))
        <?php list($view, $data) = Larapay::getPaymentModalView($currentGateway); ?>
        @include($view, $data)
    @endif
</script>
</div>
