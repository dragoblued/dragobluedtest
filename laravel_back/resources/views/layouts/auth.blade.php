<!DOCTYPE html>
<html lang="ru">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="theme-color" content="#9c7a52">
	<meta name="msapplication-navbutton-color" content="#9c7a52">
	<meta name="apple-mobile-web-app-status-bar-style" content="#9c7a52">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<title>Authorization</title>
	<link rel="shortcut icon" href="{{ asset('media/img/logo.ico') }}" type="image/x-icon">
	<link href="https://fonts.googleapis.com/css?family=Roboto:300,400" rel="stylesheet">
	<link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>
<body>
	<header class="header">
		<a href="/" class="logo">
            <img src="{{asset('media/img/logo.png')}}" class="logo__img" alt="LOGO">
		</a>
	</header>

	<div class="content auth">
		@yield('content')
	</div>

	<footer class="footer">
		© {{ date('Y') }} {{ config('app.name') }}.
	</footer>

</body>
</html>
