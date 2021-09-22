{{-- @push('styles')
    <link rel="stylesheet"
      href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/10.5.0/styles/dracula.min.css">
@endpush
@push('scripts')
    <script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/10.5.0/highlight.min.js"></script>
    <script>hljs.initHighlightingOnLoad();</script>
@endpush --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight flex justify-between">
            <span>{{ __('Invoice') }}</span>
            <span class="text-gray-400 text-sm pt-1">#{{$invoice->uuid}}</span>
        </h2>
    </x-slot>
@php
    $sub = $invoice->subscription; 
    $status = $invoice->status;

@endphp
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <?php json_encode($invoice); ?>
                <!-- Card is full width. Use in 12 col grid for best view. -->
                <!-- Card code block start -->
                <div class="flex flex-col lg:flex-row mx-auto w-full bg-white shadow rounded">
                    <div class="w-full lg:w-1/3 p-6">
                        <div class="flex items-center">
                            <div class="w-12 h-12 rounded shadow">
                                <img class="w-full h-full overflow-hidden object-cover rounded" src="https://tuk-cdn.s3.amazonaws.com/assets/components/grid_cards/gc_24.png" alt="logo" />
                            </div>
                            <div class="ml-3">
                                <h5 class="text-gray-800 font-medium text-base">
                                    @if($sub)
                                        #{{$sub->id}} {!! $sub->price->product->name !!}
                                    @else
                                        no
                                    @endif
                                </h5>
                                <p class="text-gray-600 text-xs font-normal">
                                    @if($sub)
                                        {{ $sub->price->name }}
                                    @else
                                        no sub
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center justify-between mt-8 w-full">
                            <div>
                                <p class="text-sm text-gray-800 font-normal mb-1 tracking-normal">Amount</p>
                                <h2 class="text-sm xl:text-lg text-gray-600 font-black tracking-normal">{{ Larapay::formatPrice($invoice->amount) }}</h2>
                            </div>
                            {{-- <div>
                                <p class="text-sm text-gray-800 font-normal mb-1 tracking-normal">Amount</p>
                                <h2 class="text-sm xl:text-lg text-gray-600 font-black tracking-normal">{{ Larapay::formatPrice($invoice->amount) }}</h2>
                            </div> --}}
{{--                                     <div>
                                <p class="text-sm text-gray-800 font-normal mb-1 tracking-normal">Net Paid</p>
                                <h2 class="text-sm xl:text-lg text-gray-600 font-bold tracking-normal">$888,546</h2>
                            </div> --}}
                        </div>

                        {{-- <h3 class="text-lg text-gray-800 font-bold mt-5 mb-1">User Experience Revamp</h3> --}}
                        <p class="mt-8 text-gray-600 text-sm font-normal">
                            @if($sub)
                                {!! $sub->price->product->description !!}
                            @else

                            @endif
                        </p>
                    </div>
                    <div class="flex flex-col justify-between w-full lg:w-1/3 p-6 border-t border-b lg:border-t-0 lg:border-b-0 sm:border-l sm:border-r border-gray-300">
                        <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between">
                            <div class="text-xs text-center w-full bg-indigo-100 text-indigo-700 rounded font-medium p-3 lg:mr-3">Date: {{ $invoice->created_at->format('d M Y') }}</div>
                            <div class="mt-4 text-center w-full lg:mt-0 text-xs bg-red-200 text-red-500 rounded font-medium p-3">Due Date: {{ $invoice->due_at->format('d M Y') }}</div>
                        </div>
                        <div class="flex justify-center">
                            <div class="inline-block">
                                @if($sub && $sub->isActive())
                                    <x-button-link class="sm:mt-3" href="{{ route(Larapay::getManagementRoute($sub), $sub) }}">Manage</x-button-link>
                                @endif
                            </div>
                        </div>
                    </div>

                    

                    <div class="flex flex-col justify-between w-full lg:w-1/3 p-6 relative">
                        <div class="">
                            <div class="mb-4 flex items-center justify-between">
                                <p class="text-gray-600 text-sm font-normal leading-3 tracking-normal">Status: {{ __(ucwords($status)) }}</p>
                                <p class="text-xs text-indigo-700 font-normal leading-3 tracking-normal">
                            </div>
                            <div class="relative mb-8 h-12">
                                <hr class="h-2 rounded-sm bg-gray-200" />
                                <hr class="absolute top-0 h-2 rounded-sm 
                                @if($status == 'refunded') bg-gray-600 @endif
                                @if($status == 'due') bg-yellow-400 @endif
                                @if($status == 'overdue') bg-red-400 @endif
                                @if($status == 'paid') bg-green-400 @endif
                                " style="width: {{ 100 }}%"/>
                            </div>
                        </div>
                        <div class="">
                            @livewire('larapay::invoice-manager', ['invoice' => $invoice, 'status' => $status])
                        </div>
                    </div>
                    {{-- @if(count($sub->invoices) > 1))
                        yes
                    @endif --}}
                </div>
            </div>
        </div>
    </div>
                
</x-app-layout>
