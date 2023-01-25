<select class="js-select" data-search="true" name="event_id" onload="javascript:show();" onChange="javascript:addLang(this.value);">
	<option value="0">no selected</option>
	@foreach($events as $key => $event)
		<option value="{{ $key }}" {{ isset($item) && $item->event_id === $key ? 'selected' : '' }}>{{ $event }}</option>
	@endforeach
</select>

<div class="form__line" style="margin-top: 20px;">
	<span class="form__signature form__signature_required">
		Language
	</span>
	<select name="lang">
		@if(isset($item))
			@if(isset($item->event->langs))
				@foreach($item->event->langs as $lang)
					<option value="{{ $lang }}" {{ $lang === $item->lang ? 'selected' : '' }}>{{ $lang }}</option>
				@endforeach
			@endif
		@endif
	</select>			
</div>

<script>
	function addLang(event_id){
		var langList = document.querySelector('select[name="lang"]');
		loading = document.createElement('option');
		loading.innerHTML = 'loading...';
		langList.appendChild(loading);

		$.ajax({
       		type:'GET',
       		url:`/admin/events/${event_id}`,
       		data:'_token = <?php echo csrf_token() ?>',
       		success:function(data){
          		if (data) {
          			langList.innerHTML = '';
          			data.langs.forEach((l) => {
          				var lang = document.createElement('option');
          				lang.setAttribute('value',l);
          				lang.innerHTML = l;
          				langList.appendChild(lang);
          			});
          		}
       		}
    	});
	}

	function show(){
		alert(1);
	}
</script>