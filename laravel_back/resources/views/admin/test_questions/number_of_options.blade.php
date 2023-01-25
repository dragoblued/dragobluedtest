<div class="form__line">
	<input step="any" placeholder="0" name="number_of_options" type="number" id="number_of_options" value="{{ $item->number_of_options ?? '' }}">
</div>

<div class="form__line" >
	<span class="form__signature form__signature_required">
		Options
	</span>
	<p id="placeholder"></p>
</div>
@if(isset($item))
	@if($item->type !== 'fill-in-the-blanks')
		@foreach($item->options as $option)
			<div class="content__row">
				<div class="content__col content__col_12" id="existing_options">
					<div class="form__line" >
					<input step="any" placeholder="0" name="options[]" type="text" value="{{ $option ?? '' }}">
				</div>
				</div>
			</div>
		@endforeach
	@else
	<div class="content__row">
		<div class="content__col content__col_9" id="existing_options">
			<div class="form__line">
				<p>in the place where you want to substitute the selection field, substitute @@</p>
				<textarea name="options" id="textarea-choice">{{ $item->options ?? '' }}</textarea>
			</div>
			<button onclick="function show(){event.preventDefault(); var val = document.querySelector('#textarea-choice'); var res = document.querySelector('#result-choice'); res.innerHTML=val.value.replace(/@@/g,'____');} show()">Show result</button>
		</div>
		<div class="content__col content__col_3" id="existing_options">
			<p id="result-choice"></p>
		</div>
	</div>
	@endif
@endif
<div class="content__row">
	<div class="content__col content__col_9" id="test_options" >

	</div>
	<div class="content__col content__col_3">
		<p id="result-choice"></p>
	</div>
</div>


	@if(isset($item))
		<div class="form__line">
			<span class="form__signature form__signature_required">
		       	Correct answers
			</span>
		</div>
		@foreach($item->correct_answers as $correct_answer)
			<div class="content__row">

				<div class="content__col content__col_12">
					<div class="answers">
						<div class="form__line">
							<input name="correct_answers[]" type="text" class="answer" value="{{ $correct_answer ?? '' }}">
						</div>
					</div>
				</div>
			</div>
		@endforeach
	@else
	<div class="content__row">
		<div class="content__col content__col_12">
			<div class="answers">
				<div class="form__line">
					<span class="form__signature form__signature_required">
		           	Correct answers
					</span>
					<input name="correct_answers[]" type="text" class="answer" value="">
				</div>
			</div>
		</div>
	</div>
	@endif
	<button id="btn" >
		<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-plus-square-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
  <path fill-rule="evenodd" d="M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2zm6.5 4.5a.5.5 0 0 0-1 0v3h-3a.5.5 0 0 0 0 1h3v3a.5.5 0 0 0 1 0v-3h3a.5.5 0 0 0 0-1h-3v-3z"/>
</svg>
	</button>

<script>
	var numberOptions = document.querySelector('#number_of_options');
	var type = document.querySelector('[name="type"]');

	var options = document.querySelector('#test_options');
	var existing_options = document.querySelectorAll('#existing_options');
	//console.log(existing_options[1]);
	const addOptions = () => {
		var count = +numberOptions.value;

		if (count >= 1) {
			options.innerHTML = '';
			if (existing_options.length < count) {
				//options.innerHTML = existing_options;
				count = count - existing_options.length;
			}else if(existing_options.length > count){
				count = existing_options.length - count;
				for (var i = 1; i <= count; i++) {
					existing_options[i].remove();
				}
				count = 0;
			}else if(existing_options.length == count){
				count = 0;
			}

			if (type.value === 'fill-in-the-blanks') {
					option = document.createElement("textarea")
					option.setAttribute('name','options');;
					option.setAttribute('id','textarea-choice')
					document.querySelector('#placeholder').innerHTML = 'in the place where you want to substitute the selection field, substitute @@';
					option.setAttribute('onkeyup',"function show(){event.preventDefault(); var val = document.querySelector('#textarea-choice'); var res = document.querySelector('#result-choice'); res.innerHTML=val.value.replace(/@@/g,'____');} show()");
					option.classList.add('options');
					options.appendChild(option);
			}else{
				for (var i = 1; i <= count; i++) {
					var option = document.createElement("input");
					option.setAttribute('type','text');
					option.setAttribute('name','options[]');

					option.classList.add('options');
					options.appendChild(option);

				}
			}

		}else{
			options.innerHTML = '';
			existing_options.innerHTML = '';
		}
	}

	numberOptions.addEventListener('blur', addOptions);



	const elements = document.querySelector('.answers');
	const plus = document.querySelector('#btn');

	var counter = 1;
	const addInput = (event) => {
		event.preventDefault();
		if (type.value === 'single-choice') {
			alert('You have selected a single-choice');
		}else{
			const btnDeleteProgram = document.createElement('button');
			var inputId =`inputs-${counter}`;
			var element = document.createElement("input");
			element.setAttribute('name','correct_answers[]');
			element.setAttribute('type','text');
			//element.classList.add('plan');
			element.classList.add(inputId);

			btnDeleteProgram.classList.add('btnDelete');
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

<style type="text/css">
	.options {
		margin-bottom: 10px!important;
	}

	#btn {
		display: inline-block;
		background: none;
		border: none;
		margin-left: 98%;
	}

	.btnDelete{
		background: black;
		border: 1px solid black;
		border-radius: 2em;
		color: white;
		display: inline-block;
		font-size: 12px;
		height: 17px;
		line-height: 2px;
		margin: 0 0 8px;
		margin-left: 98.5%;
		padding: 0;
		text-align: center;
		width: 17px;
	}
</style>
