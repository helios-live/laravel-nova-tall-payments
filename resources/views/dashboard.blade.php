<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 sm:px-20 bg-primary text-hero border-b border-gray-300">
                    <div>
                        <x-jet-application-logo class="block h-12 w-auto" />
                    </div>

                    <div class="mt-8 text-2xl">
                        Welcome to {{ config('app.name') }}!
                    </div>

                    <div class="mt-6 text-gray-500">
                    </div>
                </div>

                <div class="border-t border-gray-200 bg-gray-200 bg-opacity-25 grid grid-cols-1 md:grid-cols-2">
                    @php
                        $index = 0;
                        $len = count($subscriptions);
                    @endphp
                    @foreach ($subscriptions as $index => $sub)
                        @php
                            $lastline = $len % 2 ? $index == $len - 1 : $index >= $len - 2;
                            $status = $sub->status;
                        @endphp
                        <div
                            class="p-6 border-gray-200
                        @if (!$lastline) border-b @endif
                        @if (!($index % 2)) border-r @endif
                        @if ($loop->last && !($index % 2)) border-r @endif
                    ">
                            <div class="flex">
                                <div class="pt-5 w-20 flex flex-col items-center">
                                    <svg fill="none" stroke="currentColor" stroke-linecap="round"
                                        stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24"
                                        class="w-8 h-8 text-gray-400">
                                        <path
                                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                                        </path>
                                    </svg>
                                    <span
                                        class="text-xs pt-1
                                @if ($status == 'Active') text-green-400 @endif
                                @if ($status == 'Waiting') text-purple-400 @endif
                                @if ($status == 'Suspended') text-yellow-500 @endif
                                @if ($status == 'Ended') text-red-500 @endif
                                @if ($status == 'New') text-blue-400 @endif
                                ">{{ $sub->status }}</span>
                                </div>
                                <div>
                                    <div
                                        class="flex-grow ml-4 text-lg text-gray-600 leading-7 font-semibold flex justify-between">
                                        <span>{!! $sub->name !!}</span>
                                    </div>
                                    <div class="ml-4">
                                        <div class="mt-2 text-sm text-gray-500 mb-5">
                                            {!! $sub->price->product->description !!}
                                        </div>

                                        @if (!$sub->isOff())
                                            @if ($sub->isActive())
                                                <a class="py-1 px-2 bg-gray-100 rounded-md hover:bg-gray-200 shadow hover:shadow-md"
                                                    href="{{ route(Larapay::getManagementRoute($sub), $sub) }}">
                                                    {{ __('Manage') }}
                                                </a>
                                            @else
                                                <a class="py-1 px-2 bg-gray-100 rounded-md hover:bg-gray-200 shadow hover:shadow-md"
                                                    href="{{ route(Larapay::getManagementRoute($sub), $sub->latestInvoice) }}">
                                                    {{ __('Pay') }}
                                                </a>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </div>

                        </div>
                    @endforeach
                </div>
                @if ($len == 0)
                    <div class="flex items-center p-16">

                        <div class="ml-4 text-xl text-gray-600 leading-7 font-semibold">
                            {{ __("You don't have any active orders yet") }}
                            <a class="font-extrabold"
                                href="{{ route('larapay.order.product-list') }}">{{ __('Click Here') }}</a>
                            {{ __('to get started') }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
