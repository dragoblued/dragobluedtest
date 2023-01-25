@extends('errors::illustrated-layout')

@section('title', __('Page Expired'))
@section('code', '419')
@section('image')
   <div style="background-image: url({{ asset('/svg/403.svg') }});" class="absolute pin bg-cover bg-no-repeat md:bg-left lg:bg-center">
   </div>
@endsection
@section('message', __('Page Expired'))
