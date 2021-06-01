<?php $index = 0; ?>

@foreach ( $gateways as $slug => $opts )
	<a href="{{route('larapay.gateway', [ 'gateway' => $slug, 'invoice' => $invoice ])}}">
	    <div class="relative px-4 py-3 inline-flex w-full rounded-lg focus:z-10 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue {{ $index > 0 ? 'border-t border-gray-200 rounded-t-none' : '' }} {{ ! $loop->last ? 'rounded-b-none' : '' }}">
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
		</div>
</a>
	<?php $index++; ?>
@endforeach