<!DOCTYPE html>
<html lang="ru" class="admin">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <meta name="theme-color" content="#9c7a52">
   <meta name="msapplication-navbutton-color" content="#9c7a52">
   <meta name="apple-mobile-web-app-status-bar-style" content="#9c7a52">
   <meta name="csrf-token" content="{{ csrf_token() }}">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <title>{{ $page->title }}</title>
   <link rel="shortcut icon" href="{{ asset('media/img/logo.ico') }}" type="image/x-icon">
   <link rel="stylesheet" href="{{ asset('css/prestyle.css') }}">
   <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
   <link rel="stylesheet" href="{{ asset('css/inc/gallery.css') }}">
   <link rel="stylesheet" href="{{ asset('css/inc/gallery-modal.css') }}">
   <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.4.1/css/all.css" integrity="sha384-5sAR7xN1Nv6T6+dT2mhtzEpVJvfS3NScPQTrOxhwjIuvcA67KV2R5Jz6kr4abQsz" crossorigin="anonymous">
   <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
   <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
   @stack('links')
   @yield('link')
</head>
@yield('css')
<body id="admin">
@include('admin.inc._header')
@include('admin.inc.menu')
<section class="content">
   @include('admin.inc.content-header')

   @yield('content')
</section>

@include('admin.inc._footer')
<script src="{{ asset('js/libs/jquery.min.js') }}"></script>
<script src="{{ asset('js/libs/fontawesome.min.js') }}"></script>
<script src="{{ asset('js/libs/inputmask.min.js') }}"></script>
<script src="{{ asset('js/libs/jquery.inputmask.min.js') }}"></script>
<script src="{{ asset('js/libs/jquery.mask.min.js') }}"></script>
<script src="{{ asset('js/libs/jodit.min.js') }}"></script>
<script src="{{ asset('js/libs/formstyler.min.js') }}"></script>
<script src="{{ asset('js/libs/jquery.fancybox.min.js') }}"></script>
<script src="{{ asset('js/libs/jquery-ui.js') }}"></script>
<script src="{{ asset('js/libs/moment.min.js') }}"></script>
<script src="{{ asset('js/libs/daterangepicker.min.js') }}"></script>
<script src="{{ asset('js/libs/easySelect.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="{{ asset('js/libs/bootstrap.min.js') }}"></script>
<script src="{{ asset('js/libs/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('js/libs/dataTables.bootstrap4.min.js') }}"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js"></script>
@stack('ext_scripts')
<script>
   window.user = {
      id: {{auth()->user()->id}},
      name: '{{auth()->user()->name.' '.auth()->user()->surname}}',
      email: '{{auth()->user()->email}}',
   };
   window.config = {
      appUrl: '{{config("app.url")}}',
      siteUrl: '{{config("app.site_url")}}',
      CSRFToken: '{{ csrf_token() }}'
   };
</script>
<script src="{{ asset('js/admin.js') }}"></script>
@stack('scripts')
@yield('js')

</body>
</html>
