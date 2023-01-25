<!DOCTYPE html>
<html lang="ru">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="theme-color" content="#000">
	<meta name="msapplication-navbutton-color" content="#000">
	<meta name="apple-mobile-web-app-status-bar-style" content="#000">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<meta name="description" content="{{ $page->meta_d ?: '' }}">
	<meta name="keywords" content="{{ $page->meta_k ?: '' }}">
	<title>{{ $page->title ?: config('app.name') }}</title>
	<link rel="shortcut icon" href="{{ asset('media/img/logo.ico') }}" type="image/x-icon">
	<link rel="stylesheet" href="{{ asset('css/prestyle.css') }}">
	<link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
	<noscript>
		<p>Для корректной работы приложения необходим JavaScript. Инструкции, как <a href="https://www.enable-javascript.com/ru/" target="blank">включить JavaScript в вашем браузере</a>.</p>
	</noscript>

    @yield('content')

</body>
</html>
