<select class="js-select" data-search="true" name="event_id">
	@foreach($events as $key => $event)
		<option value="{{ $key }}">{{ $event }}</option>
	@endforeach
</select>

<div class="form__line" style="margin-top: 20px;">
	<span class="form__signature form__signature_required">
		Language
	</span>
	<select name="lang">
	
	</select>			
</div>

<script>
	var lang = document.querySelector('select[name="lang"]');
	console.log(lang);
</script>