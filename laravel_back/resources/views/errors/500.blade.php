@extends('errors::illustrated-layout')

@section('title', __('Server Error'))
@section('code', '500')
@section('image')
   <div style="background-image: url({{ asset('/svg/500.svg') }});" class="absolute pin bg-cover bg-no-repeat md:bg-left lg:bg-center">
   </div>
@endsection
@section('message', __('Server Error'))
