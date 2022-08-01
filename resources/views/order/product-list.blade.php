<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center">
            <span class="mr-4">{{ __('Order') }}</span>
        </h2>
    </x-slot>

    <div class="py-12">
        <livewire:larapay::order-page :products="$products">
        {{-- @livewire('larapay::order-page', ['products' => $products) --}}
    </div>
</x-app-layout>
