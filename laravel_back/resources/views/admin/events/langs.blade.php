<div class="row">
	<div class="col-12 col-md-6 text-right">
		<div class="langs">
		@if(isset($item))
			@foreach($item->langs as $lang)
				<div class="form__line">
					<input type="text" name="langs[]" value="{{ $lang ?? '' }}" class="margin" required onblur="setLang(this.value)">
				</div>
			@endforeach
		@else
			<div class="form__line">
				<input type="text" name="langs[]" class="margin" required onblur="setLang(this.value)">
			</div>
		@endif
		</div>
		<button id="btnLangs" class=" btn btn-dark btn-sm mt-2">+</button>
	</div>
</div>
<script>


// add dynamical langs
const langList = document.querySelector('#lang');

	const deleteLang = (event) => {
		event.preventDefault();
		var d = document.getElementsByClassName(event.target.value.toString());
		var arr = Array.from(d);
		for(var i=d.length -1; i >=0; --i){
			d[i].remove();
		}
	}

	const langs = document.querySelector('.langs');
	const addLangBtn = document.querySelector('#btnLangs');


	var counter = 1;
	const addLang = (event) => {
		event.preventDefault();
		if (counter > 50) {
			alert('You are reached the limit');
		}else{
			const btnDeleteLang = document.createElement('button');
			var inputId =`langs-${counter}`;
			var langInput = document.createElement("input");
			langInput.setAttribute('name','langs[]');
			langInput.setAttribute('type','text');
			langInput.setAttribute('required','required');
			langInput.classList.add(inputId);

			btnDeleteLang.classList.add('btn','btn-dark','btn-sm','my-1');
			btnDeleteLang.classList.add(inputId);
			btnDeleteLang.setAttribute('value',inputId);
			btnDeleteLang.innerHTML = '-';
			btnDeleteLang.addEventListener('click', deleteLang);

			langs.appendChild(langInput);
			langs.appendChild(btnDeleteLang);
			counter++;
		}
	}
	addLangBtn.addEventListener('click', addLang);


</script>
