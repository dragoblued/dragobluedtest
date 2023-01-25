@extends('layouts.auth')

@section('content')
<form method="POST" class="auth__form form" action="{{ route('register') }}">
    {{ csrf_field() }}
	<div class="auth__header">
		<span class="auth__title">Registration</span>
		<a href="/login" class=" auth__back back">
			<img src="{{ asset('media/img/arrow_back.svg') }}" class="img-responsive back__img" alt="img">
			<div class="back__text">Back</div>
		</a>
	</div>
	<div class="form__line">
		<input type="text" name="email" placeholder="E-mail" class="form__field{{ $errors->has('email') ? ' form__field_invalid' : '' }}" value="{{ old('email') }}" required autofocus>
		@if($errors->has('email'))
			<div class="form__error">{{ $errors->first('email') }}</div>
		@endif
	</div>

	<div class="form__line">
		<input type="password" name="password" placeholder="Password" class="form__field{{ $errors->has('password') ? ' form__field_invalid' : '' }}" required>

		@if($errors->has('password'))
			<div class="form__error">{{ $errors->first('password') }}</div>
		@endif
	</div>

	<div class="form__line">
		<input type="password" name="password_confirmation" placeholder="Confirm your password" class="form__field{{ $errors->has('password_confirmation') ? ' form__field_invalid' : '' }}" required>

		@if($errors->has('password_confirmation'))
			<div class="form__error">{{ $errors->first('password_confirmation') }}</div>
		@endif
	</div>

	<div class="form__submit">
		<input type="submit" value="Register" class="button button_wide" autofocus><br>
	</div>
</form>
@endsection
