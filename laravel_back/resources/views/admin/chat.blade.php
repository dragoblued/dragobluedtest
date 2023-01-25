@extends('layouts.admin')
@section('link')
   <link rel="stylesheet" href="{{ asset('css/inc/chat.css') }}">
@endsection
@section('content')
   <div class="container">
      @include('admin.inc.chat', [
               'displayChatList' => true,
               'displayChatDialog' => true,
               'displayChatDialogHeader' => true,
               'displayChatDialogFooter' => true,
               'emptyDialogMessage' => 'Select user and lesson',
               'chatClassName' => 'my-4'
            ])
      <div class="alert alert-warning text-right">Double click on message - make a reference. Right mouse button click on message - options</div>
   </div>
   @include('admin.inc.modal-dialog')
@endsection
@section('js')
   <script src="{{ asset('js/inc/show-user-info.js') }}"></script>
   <script>
      setChatVars(window.user.id, window.user.name ?? window.user.email);
   </script>
@endsection
