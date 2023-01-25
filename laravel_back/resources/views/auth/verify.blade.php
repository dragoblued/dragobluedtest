@extends('layouts.auth')

@section('content')
<div class="auth__form form">
	<div class="auth__header">
		<a href="/login" class=" auth__back back">
			<img src="{{ asset('media/img/arrow_back.svg') }}" class="img-responsive back__img" alt="img">
			<div class="back__text">Back</div>
		</a>
	</div>
	<div class="form__line">
	    {{ __('Check your email for confirmation link to continue') }}
	</div>
	<div class="form__line">
	    {{ __('If you has not recieve the email') }},
	    <a href="{{ route('verification.resend') }}" class="form__link">{{ __('click to send new one') }}</a>
	</div>
	@if (session('resent'))
	    <div class="form__line alert alert-success" role="alert">
	        {{ __(session('resent')) }}
	    </div>
	@endif
</div>
@endsection
