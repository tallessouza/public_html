<?php
	$wrapper_classname = 'px-12 pt-7 pb-11 rounded-3xl text-center';

	if ( $featured ) {
		$wrapper_classname .= ' border';
	}

	$currencySymbol = $currency ?? currency() ->symbol;
?>

<div class="{{$wrapper_classname}} max-xl:px-6 max-lg:px-4">
	<h6 class="p-[0.35rem] mb-6 border rounded-md text-[11px] text-opacity-80">{{__($title)}}</h6>
	<p class="text-[45px] font-medium text-heading-foreground leading-none -tracking-wide mb-1">

		@if(currencyShouldDisplayOnRight($currencySymbol))
		  	{{$price}}<sup class="text-[0.53em]">{{$currency}}</sup>
		@else
			<sup class="text-[0.53em]">{{$currency}}</sup>{{$price}}
		@endif

	</p>
	<p class="mb-4 text-sm opacity-60">{{ __('per '.$period) }}</p>
	<a href="{{$buttonLink}}" class="block w-full p-3 mb-6 rounded-lg bg-black text-heading-foreground bg-opacity-[0.03] font-medium hover:bg-black hover:text-white transition-colors">{{__($buttonLabel)}}</a>
	<ul class="px-3 text-left max-lg:p-0">
		@if($trialDays > 0)
		<li class="flex items-center mb-4">
			<span class="inline-grid place-content-center w-[22px] h-[22px] mr-3 rounded-xl text-[#684AE2] bg-[#684AE2] bg-opacity-10 shrink-0">
				<svg width="13" height="10" viewBox="0 0 13 10" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
					<path d="M3.952 7.537L11.489 0L12.452 1L3.952 9.5L1.78814e-07 5.545L1 4.545L3.952 7.537Z"/>
				</svg>
			</span>
			{{ number_format($trialDays)." ".__('Days of free trial.') }}
		</li>
		@endif
		@if ( !empty( $activeFeatures ) )
			@foreach( explode( ',', $activeFeatures ) as $feature )
				<li class="flex items-center mb-4">
					<span class="inline-grid place-content-center w-[22px] h-[22px] mr-3 rounded-xl text-[#684AE2] bg-[#684AE2] bg-opacity-10 shrink-0">
						<svg width="13" height="10" viewBox="0 0 13 10" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
							<path d="M3.952 7.537L11.489 0L12.452 1L3.952 9.5L1.78814e-07 5.545L1 4.545L3.952 7.537Z"/>
						</svg>
					</span>
					{{ trim( __($feature) ) }}
				</li>
			@endforeach
		@endif
		<li class="mb-[0.625em]">
			<span class="inline-grid place-content-center w-[22px] h-[22px] mr-3 rounded-xl text-[#684AE2] bg-[#684AE2] bg-opacity-10 shrink-0">
				<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
			</span>
			@if((int)$totalWords >= 0)
			<strong>{{number_format($totalWords)}}</strong> {{__('Word Tokens')}}
			@else
			<strong>{{__('Unlimited')}}</strong> {{__('Word Tokens')}}
			@endif
		</li>
		<li class="mb-[0.625em]">
			<span class="inline-grid place-content-center w-[22px] h-[22px] mr-3 rounded-xl text-[#684AE2] bg-[#684AE2] bg-opacity-10 shrink-0">
				<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
			</span>
			@if((int)$totalImages >= 0)
			<strong>{{number_format($totalImages)}}</strong> {{__('Image Tokens')}}
			@else
			<strong>{{__('Unlimited')}}</strong> {{__('Image Tokens')}}
			@endif
		</li>
		@if ( !empty( $inactiveFeatures ) )
			@foreach( explode( ',', $inactiveFeatures ) as $feature )
				<li class="flex items-center mb-4 opacity-25">
					<span class="inline-grid place-content-center w-[22px] h-[22px] mr-3 rounded-xl text-[#684AE2] bg-[#684AE2] bg-opacity-10 shrink-0">
						<svg width="5" height="2" viewBox="0 0 5 2" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
							<path d="M0 0.00299835H4.167V1.539H0V0.00299835Z"/>
						</svg>
					</span>
					{{ trim( __($feature) ) }}
				</li>
			@endforeach
		@endif
	</ul>
</div>