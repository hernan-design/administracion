<div class="no-results-filter hidesoft">
	@if( isset($message) )
		<p class="text-center"><em>{!! $message !!}</em></p>
	@else
		<p class="text-center"><em>{{ Lang::get('app.noresults') }}</em></p>
	@endif
</div>
