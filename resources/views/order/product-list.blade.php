<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center">
            <span class="mr-4">{{ __('Order') }}</span>
            <livewire:sound-onoff />
        </h2>
    </x-slot>

    <div class="py-12">
        <livewire:order-page :products="$products">
    </div>
</x-app-layout>
