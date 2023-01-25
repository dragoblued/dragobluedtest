@extends('layouts.auth')

@section('content')

<form class="auth__form form" method="POST" action="{{ route('login') }}">
	{{ csrf_field() }}
	<div class="auth__header">
        <span class="auth__title">Authorization</span>
    </div>

{{--	<div class="form__line">--}}
{{--        Attention! The app has been updated. To work correctly, you need to clear the cache on all pages of the app. <a href="https://qwerty.ru/help/settings/100/376085/" target="_blank">How do I clear the browser cache?</a>--}}
{{--	</div>--}}

	<div class="form__line">
		<input type="text" name="email" placeholder="E-Mail or username" class="form__field{{ $errors->has('email') ? ' form__field_invalid' : '' }}" value="{{ old('email') }}" required autofocus>
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

	{{--<label class="form__line form__line_remember">
		<input type="checkbox" name="remember" value="{{ old('remember') ? 'checked' : '' }}">Запомнить меня
	</label>--}}

	<div class="form__submit">
		<input type="submit" value="Enter" class="button button_wide"><br><br>
{{--		<a class="form__link" href="{{ route('register') }}">Register</a>--}}
		<a class="form__link" href="{{ route('password.request') }}">Forgot your password?</a>
	</div>
</form>

@endsection
