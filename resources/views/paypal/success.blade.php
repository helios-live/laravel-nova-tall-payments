<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Paypal Success') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="text-2xl p-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                Payment Received
            </div>
        </div>
    </div>
</x-app-layout>
