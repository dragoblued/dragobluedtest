@extends('layouts.admin')

@isset($ext_links)
   @push('links')
      @foreach($ext_links as $ext_link)
         {!! $ext_link !!}
      @endforeach
   @endpush
@endisset

@section('content')

   <!-- Ошибки валидации -->
   @if($errors->all())
      <div class="errors">

         @foreach($errors->all() as $error)
            <div class="errors__item">
               {!! preg_replace('"The( selected)? (.*?) (field|is|may|must|does|has)(.*)?"', 'The$1 <b>$2</b> $3$4', $error) !!}
            </div>
         @endforeach

      </div>
   @endif

   <!-- Открытие формы -->
   @if(!isset($item))
      {{ Form::model(null, [
         'route' => "{$page->route}.store",
         'files' => true,
         'class' => 'form container-fluid'
      ]) }}
   @else
      {{ Form::model($item, [
         'route' => [ "{$page->route}.update", $item->id ?? $item['id'] ],
         'files' => true,
         'class' => 'form container-fluid'
      ]) }}
      {{ method_field('PUT') }}
   @endif



   <!-- Формирование полей -->
   <div class="row">

      @foreach ($form as $key => $line)

         <div class="col-sm-{{ $line->class }}">

            {!!
               property_exists($line, 'line')
               ? '<div class="form__line">'
               : '<label class="form__line">'
            !!}

            @if($line->type !== 'checkbox')
               <span class="form__signature {{ $line->required ? ' form__signature_required' : '' }}">
				{!! $line->signature !!}
			</span>
            @endif

            @if($line->type == 'hidden')
               <input type="hidden" name="{{$key}}" value="{{$item[$key] ?? $line->default ?? ''}}">
            @elseif($line->type == 'text')
               {{ Form::text($key, $item[$key] ?? $line->default ?? '', ['required' => $line->required ?? null]) }}
            @elseif($line->type == 'password')
               {{ Form::password($key, null, ['required' => $line->required ?? null]) }}
            @elseif($line->type == 'email')
               {{ Form::email($key, $item[$key] ?? '', ['required' => $line->required ?? null]) }}
            @elseif($line->type == 'tel')
               {{ Form::tel($key, $item[$key] ?? '', ['required' => $line->required ?? null, 'class' => 'phone-inputmask']) }}
            @elseif($line->type == 'number')
               {{ Form::number(
                     $key,
                     $item[$key] ?? $line->default ?? 0,
                     [ 'step' => $line->step ?? 'any', 'min' => $line->min ?? null,
                     'max' => $line->max ?? null, 'required' => $line->required ?? null]
                 ) }}
            @elseif($line->type == 'order_eurodentist')
               {{ Form::input('number', 'order', $line->item ?? 100) }}
            @elseif($line->type == 'time')
               {{ Form::time($key, $item[$key] ?? $line->default ?? '00:00', ['required' => $line->required ?? null]) }}
            @elseif($line->type == 'timestamp')
               {{ Form::date($key, $item[$key] ?? $line->default ?? date('Y-m-d'),
                  [ 'class' => 'timestamp', 'required' => $line->required ?? null ]) }}
            @elseif($line->type == 'textarea')
               {{ Form::textarea($key, $item[$key] ?? $line->default ?? '', ['required' => $line->required ?? null]) }}
            @elseif($line->type == 'wysiwyg')
               {{ Form::textarea(
                  $key,
                  $item[$key] ?? null,
                  [
                     'class' => 'wysiwyg js-wysiwyg',
                     'data-uploader' => route(
                        'admin.uploader',
                        [
                           'folder' => $line->media
                        ]
                     ),
                     'data-filebrowser' => route(
                        'admin.browser',
                        [
                           'folder' => $line->media
                        ]
                     ),
                  ]
               ) }}
            @elseif($line->type == 'select')
               {{ Form::select($key, $line->items, $item[$key] ?? $line->default ?? null, [
                  'class' => 'js-select',
                  'data-search' => 'true',
                  'required' => $line->required ?? null
               ]) }}
            @elseif($line->type == 'checkbox')
               @if(!property_exists($line, 'items'))
                  <div class="custom-control custom-checkbox mt-2 mb-3">
                     {{ Form::checkbox($key, true, isset($item[$key]) ? $item[$key] == true : false, ['class' => 'custom-control-input', 'id' => $key]) }}
                     <label class="custom-control-label cursor-pointer" for="{{$key}}">{!! $line->signature !!}</label>
                  </div>
               @else
                  @foreach($line->items as $val => $option)
                     <label>
                        {{ Form::checkbox("{$key}[]", $val, !isset($item[$key]) ? false : (in_array($val, $item[$key]) ? true : false)) }}
                        {!! $option !!}
                     </label><br>
                  @endforeach
               @endif

            @elseif($line->type == 'include')
               @include("{$page->route}.{$key}")
            @elseif($line->type == 'files')
               {{ Form::file($line->multiple ? "{$key}[]" : $key, [
                  'accept'   => $line->mimes,
                  'class'    => 'js-files__input',
                  'multiple' => $line->multiple,
                  'disabled' => $line->disabled ?? null
               ]) }}
               <input type="checkbox" class="d-none js-files__delete" name="{{$key}}_delete" id="{{$key}}_delete">
               @include('admin._files')
            @endif

            {!!
               property_exists($line, 'line')
               ? "</div>"
               : "</label>"
            !!}

         </div>
      @endforeach

   </div>

   <!-- Кнопка отправки формы -->
   @if(!isset($item))
      {{ Form::submit('Create', [ 'class' => 'submit-button' ]) }}
   @else
      {{ Form::submit('Save', [ 'class' => 'submit-button' ]) }}
   @endif

   <!-- Закрытие формы -->
   {{ Form::close() }}
@endsection

@isset($ext_scripts)
   @push('ext_scripts')
      @foreach($ext_scripts as $ext_script)
         {!! $ext_script !!}
      @endforeach
   @endpush
@endisset
@isset($scripts)
   @push('scripts')
      @foreach($scripts as $script)
         {!! $script !!}
      @endforeach
   @endpush
@endisset
