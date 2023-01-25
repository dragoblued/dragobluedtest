@extends('layouts.admin')
@section('content')
   <div class="container-fluid">
      <div class="row">
         <div class="col-12" style="margin: 0 auto!important">
            {{ Form::model(null, [
                  'route' => [ "{$page->route}.update", 1 ],
                  'files' => true,
                  'class' => 'form'
               ])
            }}
            {{ method_field('PUT') }}
            <div id="map" style="height: 400px; width: 100%;"></div>
            @foreach($items as $address)
               <div class="form__line">
                  @if($address->key === 'address' || $address->key === 'address_building_name')
                     <label class="form__signature">{{ str_replace('_',' ',ucfirst($address->key)) }}</label>
                     <input type="text" name="{{ $address->key }}" value="{{$address->value}}">
                  @else
                     <input type="hidden" name="{{ $address->key }}" value="{{$address->value}}">
                  @endif
               </div>
            @endforeach
            <div class="mt-3">
               {{ Form::submit('Save', [ 'class' => 'button' ]) }}
            </div>
            {{ Form::close() }}
         </div>
      </div>
   </div>
   <script src="https://api-maps.yandex.ru/2.1/?lang=en_US&amp;apikey=fa48a0b8-8df2-49a1-a883-e47fa0e304a6" type="text/javascript"></script>
   <script src="{{ asset('js/inc/yandex-map.js') }}"></script>
   <script>
      setMapVars({!! $coords !!});
   </script>

@endsection
