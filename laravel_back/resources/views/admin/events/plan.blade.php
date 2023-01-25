<div class="row">
	<div class="col-12 col-md-6 text-right">
		<div class="plans">
		@if(isset($item))
			@foreach($item->plan as $plan)
				<div class="form__line">
					<input name="plan[]" type="text" class="plan" value="{{ $plan ?? '' }}">
				</div>
			@endforeach
		@else
			<div class="form__line">
				<input name="plan[]" type="text" class="plan">
			</div>
		@endif
		</div>

		<button id="btn" class="btn btn-dark btn-sm mt-2">+</button>
	</div>
</div>

<script>
	const elements = document.querySelector('.plans');
	const plus = document.querySelector('#btn');

	var counter = 1;
	const addInput = (event) => {
		event.preventDefault();
		if (counter > 50) {
			alert('You are reached the limit');
		}else{
			const btnDeleteProgram = document.createElement('button');
			var inputId =`inputs-${counter}`;
			var element = document.createElement("input");
			element.setAttribute('name','plan[]');
			element.setAttribute('type','text');
			element.classList.add('plan');
			element.classList.add(inputId);

			btnDeleteProgram.classList.add('btn','btn-dark','btn-sm','my-1');
			btnDeleteProgram.classList.add(inputId);
			btnDeleteProgram.setAttribute('value',inputId);
			btnDeleteProgram.innerHTML = '-';
			btnDeleteProgram.setAttribute('onclick',"function i(inputId){event.preventDefault(); var d = document.getElementsByClassName(event.target.value.toString()); console.log(d); var arr = Array.from(d); for(var i=d.length -1; i >=0; --i){ d[i].remove();}} i()");

			elements.appendChild(element);
			elements.appendChild(btnDeleteProgram);
			counter++;
		}
	}
	plus.addEventListener('click', addInput);
</script>
