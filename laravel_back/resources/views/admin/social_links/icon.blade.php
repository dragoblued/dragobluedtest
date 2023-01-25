Icon names must be used from the 
<a target="_blank" href="https://fontawesome.com/icons?d=gallery&s=brands">Font Awesome 
	<i class="{{ isset($item) ? 'fab fa-'.$item['icon'] : '' }}" style="color: black" id="result"></i>
</a> 
<input name="icon" id="icon" type="text" value="{{ $item['icon'] ?? '' }}">

<script>
	const icon = document.querySelector('#icon');
	const result =  document.querySelector('#result');
	icon.addEventListener('keyup', function(event){
		result.setAttribute('class',`fab fa-${event.target.value}`)
	})
</script>