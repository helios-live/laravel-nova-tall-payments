<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg">
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
                <div x-data="{status:['Active', 'New'], visible: 0}" @set-status="visible = 0; status = $event.detail">
                    <div class="relative border-t border-gray-200 bg-gray-200 bg-opacity-25 grid grid-cols-1 md:grid-cols-2">
                        <div x-show="false" class="text-xl p-4 text-center col-span-full">Loading...</div>
                        <div class="absolute right-2 top-2">
                            <x-jet-dropdown align="right" width="120">
                                <x-slot name="trigger">
                                    <a href="javascript:" class="text-gray-500">
                                        <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="filter" class="fill-current hover:text-black" role="img" xmlns="http://www.w3.org/2000/svg" width="16"
                                        height="16" viewBox="0 0 512 512"><path fill="currentColor" d="M487.976 0H24.028C2.71 0-8.047 25.866 7.058 40.971L192 225.941V432c0 7.831 3.821 15.17 10.237 19.662l80 55.98C298.02 518.69 320 507.493 320 487.98V225.941l184.947-184.97C520.021 25.896 509.338 0 487.976 0z"></path></svg>
                                    </a>
                                </x-slot>
                                <x-slot name="content">
                                    <div class="w-64" x-data="{status: ['Active', 'New']}" @set-status="status = $event.detail">

                                        <x-jet-dropdown-link href="javascript: " x-bind:class="{'font-extrabold': status.toString() == 'Active,New'}" @click="$dispatch('set-status', ['Active', 'New'])">
                                            {{ __('Active & New') }}
                                        </x-jet-dropdown-link>

                                        <x-jet-dropdown-link href="javascript: " x-bind:class="{'font-extrabold': status.toString() == 'Ended,Suspended'}"  @click="$dispatch('set-status', ['Ended', 'Suspended'])">
                                            {{ __('Ended') }}
                                        </x-jet-dropdown-link>

                                        <x-jet-dropdown-link href="javascript: " x-bind:class="{'font-extrabold': status.toString() == ''}"  @click="$dispatch('set-status', [])">
                                            {{ __('All') }}
                                        </x-jet-dropdown-link>
                                    </div>
                                </x-slot>
                            </x-jet-dropdown>
                        </div>
                        @php
                            $index = 0;
                            $len = count($subscriptions);
                        @endphp
                        @foreach ($subscriptions as $index => $sub)
                            @php
                                $lastline = $len % 2 ? $index == $len - 1 : $index >= $len - 2;
                                $status = $sub->status;
                            @endphp
                            <div x-cloak x-show="(!status.length || status.indexOf('{{ $status }}') != -1) && visible++"
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
                    <div x-cloak x-show="visible==0" class="flex items-center p-16">

                        <div class="ml-4 text-xl text-gray-600 leading-7 font-semibold">
                            {{ __("You don't have any orders here") }}
                            <a class="font-extrabold"
                                href="{{ route('larapay.order.product-list') }}">{{ __('Click Here') }}</a>
                            {{ __('to get started') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
