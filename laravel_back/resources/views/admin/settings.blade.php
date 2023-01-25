@extends('layouts.admin')
@section('content')
   <!-- Ошибки валидации -->
   <div class="container-fluid">
      @if($errors->all())
         <div class="errors">

            @foreach($errors->all() as $error)
               <div class="errors__item">
                  {!! preg_replace('"The( selected)? (.*?) (field|is|may|must|does|has)(.*)?"', 'The$1 <b>$2</b> $3$4', $error) !!}
               </div>
            @endforeach

         </div>
      @endif
   <!-- Формирование полей -->
      {{ Form::model(null, [
            'route' => [ "{$page->route}.update", 1 ],
            'files' => true,
            'class' => 'form'
         ]) }}
      {{ method_field('PUT') }}
      <div class="row">
         <div class="form-group col-12 col-lg-4">
            <span class="form__signature">Title</span>
            {{ Form::text('title', $items['title'] ?? '') }}
         </div>

         <div class="form-group col-12 col-lg-4">
            <span class="form__signature">Email</span>
            {{ Form::email('email', $items['email'] ?? '') }}
         </div>

         <div class="form-group col-12 col-lg-3">
            <span class="form__signature">Phone</span>
            {{ Form::text('phone', $items['phone'] ?? '', ['class' => 'phone-mask']) }}
         </div>


         <div class="form-group col-12 col-lg-4">
            <span class="form__signature">Copyright text</span>
            {{ Form::text('copyright_text', $items['copyright_text'] ?? '') }}
         </div>

{{--         <div class="form-group col-12 col-lg-4">--}}
{{--            <span class="form__signature">Email title</span>--}}
{{--            {{Form::text('email_title', $items['email_title'] ?? '')}}--}}
{{--         </div>--}}

         <div class="form-group col-12 col-lg-6">
            <span class="form__signature">Email newsletter caption</span>
            {{Form::textarea('email_newsletter', $items['email_newsletter'] ?? '')}}
         </div>

         <div class="form-group col-12 col-lg-4">
            <div class="custom-control custom-switch">
               <input type="checkbox" class="custom-control-input cursor-pointer" name="is_payment_enabled"
                      id="is_payment_enabled" value="1"
                      {{$items['is_payment_enabled'] ? 'checked="checked' : ''}}>
               <label class="custom-control-label cursor-pointer" for="is_payment_enabled">Payment service</label>
            </div>
         </div>

         <div class="form-group col-12">
            <label class="d-block mb-2" for="terms_conditions">Terms conditions</label>
            <textarea name="terms_conditions" id="terms_conditions" rows="10" class="wysiwyg js-wysiwyg"
                      data-uploader="{{route('admin.uploader', ['folder' => 'media/wisiwyg/settings/'])}}"
                      data-filebrowser="{{route('admin.browser', ['folder' => 'media/wisiwyg/settings/'])}}"
            >{{$items['terms_conditions'] ?? ''}}</textarea>
         </div>

         <div class="form-group col-12">
            <label class="d-block mb-2" for="privacy_policy">Privacy policy</label>
            <textarea name="privacy_policy" id="privacy_policy" rows="10" class="wysiwyg js-wysiwyg"
                      data-uploader="{{route('admin.uploader', ['folder' => 'media/wisiwyg/settings/'])}}"
                      data-filebrowser="{{route('admin.browser', ['folder' => 'media/wisiwyg/settings/'])}}"
            >{{$items['privacy_policy'] ?? ''}}</textarea>
         </div>
      </div>
      <button type="submit" class="submit-button">Save</button>
   </div>
   {{ Form::close() }}
@endsection
