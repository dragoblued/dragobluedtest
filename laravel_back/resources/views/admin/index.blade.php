@extends('layouts.admin')
@section('content')

@if(count($configs))
	<div class="errors">

	@foreach($configs as $error)
		<div class="errors__item">
			{!! $error !!}
		</div>
	@endforeach

	</div>
@endif

@endsection
