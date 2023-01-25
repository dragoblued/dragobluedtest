<div id="map" style="height: 400px; width: 100%; margin-bottom: 10px;"></div>
<div class="row">
   <div class="col-12">
      <div class="form__line">
         <label class="form__signature">Address</label>
         <input type="text" name="address" value="{{ isset($item) ? $item->address : '' }}" id="address">
      </div>
   </div>
   <div class="col-12">
      <div class="form__line">
         <label class="form__signature">Address Label</label>
         <input type="text" name="address_building_name" value="{{ isset($item) ? $item->address_building_name : '' }}">
      </div>
   </div>
   <input type="hidden" name="address_url" value="{{ isset($item) ? $item->address_url : '' }}">
   <input type="hidden" name="address_coordinates" value="{{ isset($item) ? json_encode($item->address_coordinates) : '' }}">
</div>



<script src="https://api-maps.yandex.ru/2.1/?lang=en_US&amp;apikey=fa48a0b8-8df2-49a1-a883-e47fa0e304a6" type="text/javascript"></script>
<script src="{{ asset('js/inc/yandex-map.js') }}"></script>
<script>
   setMapVars({!! isset($item) ? json_encode($item->address_coordinates) : null !!});
</script>
