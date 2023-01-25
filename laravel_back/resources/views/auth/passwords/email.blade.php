@extends('layouts.auth')

@section('content')

<form class="auth__form form" method="POST" action="{{ route('password.email') }}">
    {{ csrf_field() }}
    <div class="auth__header">
        <span class="auth__title">Reset password</span>
        <a href="/login" class=" auth__back back">
            <img src="{{ asset('media/img/arrow_back.svg') }}" class="img-responsive back__img" alt="img">
            <div class="back__text">Back</div>
        </a>
    </div>

    <div class="form__line">
        <input type="text" name="email" placeholder="Your E-Mail Address" class="form__field{{ $errors->has('email') ? ' form__field_invalid' : '' }}" value="{{ old('email') }}" required autofocus>

        @if($errors->has('email'))
            <div class="form__error">{{ $errors->first('email') }}</div>
        @endif
    </div>
    <div class="form__submit">
        <input type="submit" value="Send a link to change your password" class="button button_wide">
    </div>
</form>
@endsection
