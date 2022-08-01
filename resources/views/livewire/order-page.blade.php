@php
    $get_value = function($key, $value) {
        if ($key == 'country_code' ) {
            return config('countries.' . $value);
        }
        return $value;
    }
@endphp
<div>
    {{-- Nothing in the world is as soft and yielding as water. --}}

    <form method="POST" wire:submit.prevent="orderProduct()" id="orderForm">
        <div class="flex max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="w-1/2" x-data="{ open: false}">
                <x-jet-validation-errors class="mb-4" />

                @csrf
                @foreach ($products as $product)
                    @php
                        $have = $totals[$product->getKey()] ?? false;
                        $sku = !is_null($product->skumodel);
                        $soldOut = $sku && !$have;
                    @endphp
                    <div class="@if ($soldOut) bg-gray-300 text-gray-400 @else bg-white @endif shadow-sm hover:shadow-lg hover:-top-0.5 hover:-right-0.5 sm:rounded-lg m-5 p-8 relative overflow-hidden"
                        :class="{'-top-0.5 -right-0.5 shadow-lg' : open == {{ $product->id }} }">
                        <div @click="open= {{ $product->id }};"
                            x-show="open != {{ $product->id }}"
                            class="sm:rounded-lg absolute top-0 right-0 bottom-0 left-0 cursor-pointer opacity-0 z-20">
                        </div>
                        <h3 class="text-2xl flex justify-between items-center relative z-0">
                            <span>{!! $product->name !!}</span>
                            @if ($sku)
                                <span
                                    class="whitespace-nowrap inline-block text-center w-40 text-sm font-bold @if ($have) bg-yellow-500 @else bg-red-500 @endif text-white transform rotate-45 absolute top-0 -right-16">
                                    @if ($have)
                                        Only {{ $have }} left
                                    @else
                                        Sold Out
                                    @endif
                                </span>
                            @endif
                        </h3>
                        <p class="@if ($soldOut) text-gray-400 @else text-gray-600 @endif">{!! $product->description !!}</p>
                        <div :class="{'hidden': open != {{ $product->id }}}" class="mt-5 hidden">
                            @php
                                $prices = $product
                                    ->prices()
                                    ->active()
                                    ->get();
                            @endphp
                            @foreach ($prices as $price)
                                @php
                                    $tooFew = false;
                                    if ($sku && $price->payload->Count > $have) {
                                        $tooFew = true;
                                    }
                                @endphp

                                <div class="relative">
                                    @if ($tooFew)
                                        <div
                                            class="flex items-center justify-center absolute w-full h-full top-0 left-0 text-red-500 text-7xl whitespace-nowrap -rotate-12 transform z-50 opacity-50">
                                            Sold Out</div>
                                    @endif
                                    <label for="pr{{ $product->slug }}{{ $price->id }}"
                                        class="{{ $tooFew ? 'cursor-not-allowed' : 'cursor-pointer' }}">

                                        <ul
                                            class="{{ $tooFew ? 'opacity-30 bg-gray-100 shadow-inner' : 'bg-white hover:shadow-lg' }} rounded-lg border p-3 mb-5 shadow  active:shadow-sm">
                                            <li class="flex justify-between border-b pb-3 mb-3 text-xl">
                                                <div>
                                                    <input {{ $tooFew ? 'disabled' : '' }}
                                                        wire:click="selectPrice('{{ $price->id }}')"
                                                        id="pr{{ $product->slug }}{{ $price->id }}" type="radio"
                                                        class="form-input cursor-pointer" name="price_slug"
                                                        value="{{ $price->slug }}">
                                                    <span>{{ $price->name }}</span>
                                                </div>
                                                <span>
                                                    <sub class="text-gray-400">{{ $price->period }}</sub>
                                                    {{ Larapay::formatPrice($price->amount) }}
                                                </span>
                                            </li>
                                            @forelse ($price->payload ?? [] as $key => $value)
                                                <li class="mb-3">
                                                    <span>{{ $key }}:</span>
                                                    <span>{!! config("larapay.feature_map.$key")[$value] ?? config("larapay.feature_map.$value") ?? $value !!}</span>
                                                </li>
                                            @empty
                                                &nbsp;
                                            @endforelse
                                        </ul>
                                    </label>
                                </div>
                            @endforeach
                        </div>

                        <div :class="{'hidden': open != {{ $product->id }}}" class="mb-5 p-3 border-t hidden">
                            @php
                                $activeOrder = $selectedPrice && $selectedPrice->product->getKey() == $product->getKey();
                            @endphp
                            <x-jet-button data-active="{{ $activeOrder }}"
                                data-sfx="{{ $activeOrder ? 'order' : 'oups' }}"
                                class="float-right {{ $activeOrder ? '' : 'opacity-30' }}"
                                @click="setTimeout(function(){ $event.target.dataset.active && $wire.orderProduct() }, 333)"
                                type="button">
                                Order
                            </x-jet-button>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="w-1/2">
                @if ($filtered)
                    @php
                        $haveProducts = false;
                    @endphp
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg m-5 p-8">
                        @foreach ($filters as $key => $f)
                            @php
                                $selected = isset($internal_filter[$filters[$key]]);
                                $f = $filtered->pluck($filters[$key])->unique();
                            @endphp

                            @if (count($f))
                                @php
                                    $haveProducts = true;
                                @endphp
                                <div
                                    class="flex justify-between items-center bg-white rounded-lg border p-3 mb-5 shadow hover:shadow-lg active:shadow-sm">

                                    <span
                                        class="text-xl">{{ ucwords(str_replace('_', ' ', $filters[$key])) }}</span>
                                    <div class="w-1/2 flex items-center">
                                        <select wire:change="setFilter('{{ $filters[$key] }}',$event.target.value)"
                                            class="w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                                            @if (!$selected)
                                                <option disabled selected></option>
                                            @endif
                                            @foreach ($f as $i)
                                                <option value="{{ $i }}">
                                                    {{ $get_value($filters[$key], $i) }}</option>
                                            @endforeach
                                        </select>
                                        @if ($selected)
                                            <a class="ml-3 cursor-pointer font-extrabold text-2xl"
                                                wire:click="unsetFilter('{{ $filters[$key] }}')">×</a>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        @endforeach
                        @if (!$haveProducts)
                            Not enough products to enable filtering
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </form>
</div>
