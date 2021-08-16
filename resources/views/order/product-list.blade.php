<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Order') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="flex max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-jet-validation-errors class="mb-4" />

            <form method="POST" action="{{ route('larapay.order.product') }}">
                @csrf
                @foreach ($products as $product)
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg m-5 p-8">
                    <h3 class="text-2xl">
                        {{ $product->name }}
                    </h3>
                    <p class="text-gray-600">{!! $product->description !!}</p>
                    <div class="mt-5">
                        @foreach ($product->prices as $price)
                        <label for="pr{{$product->slug}}{{$price->id}}" class="cursor-pointer">
                            <ul class="bg-white rounded-lg border p-3 mb-5 shadow hover:shadow-lg active:shadow-sm">
                                <li class="flex justify-between border-b pb-3 mb-3 text-xl">
                                    <div>
                                        <input id="pr{{$product->slug}}{{$price->id}}" type="radio" class="form-input cursor-pointer" name="price_slug" value="{{ $price->slug }}">
                                        <span>{{ $price->name }}</span>
                                    </div>
                                    <span>
                                        <sub class="text-gray-400">{{ $price->period }}</sub>
                                        {{Larapay::formatPrice($price->amount)}}
                                    </span>
                                </li>
                                @foreach($price->payload as $key => $value)
                                <li class="mb-3">
                                    <span>{{ $key }}:</span>
                                    <span>{{ $value }}</span>
                                </li>
                                @endforeach
                            </ul>
                        </label>
                        @endforeach
                    </div>

                    <div class="mb-5 p-3 border-t">
                        <x-jet-button class="float-right">Order</x-jet-button>
                    </div>
                </div>
                @endforeach
            </form>
        </div>
    </div>
</x-app-layout>
