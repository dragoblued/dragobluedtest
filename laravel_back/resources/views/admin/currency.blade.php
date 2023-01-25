@extends('layouts.admin')
@section('content')
<!-- Формирование полей -->
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

		<div class="form__line">
				<label class="form__line">
					<span class="form__signature">
						Currency
					</span>
				</label>
				@foreach($items as $currency)
					<input type="radio" value="{{$currency->code}}" name="currency" id="radioButton" {{ isset($currency->code) && $currency->selected === true ? 'checked' : '' }}>{{$currency->code}}
				@endforeach
			</div>
			<hr>
			<div class="form__line content__col_3" style="display: inline-block;">
				<label class="form__line">
					<span class="form__signature">
						<i>Code</i>
					</span>
				</label>
				@foreach($items as $currency)
					{{ Form::text('code[]', $currency->code ?? '') }}
					<div style="margin-top: 10px;"></div>
				@endforeach
			</div>

			<div class="form__line content__col_3" style="display: inline-block;">
				<label class="form__line">
					<span class="form__signature">
						<i>Name</i>
					</span>
				</label>
				@foreach($items as $currency)
					{{ Form::text('name[]', $currency->name ?? '') }}
					<div style="margin-top: 10px;"></div>
				@endforeach
			</div>

			<div class="form__line content__col_3" style="display: inline-block;">
				<label class="form__line">
					<span class="form__signature">
						<i>Sign</i>
					</span>
				</label>
				@foreach($items as $currency)
					{{ Form::text('sign[]', $currency->sign ?? '') }}
					<div style="margin-top: 10px;"></div>
				@endforeach
			</div>
			<div class="form__line">
				{{ Form::submit('Save', [ 'class' => 'button' ]) }}
			</div>
	{{ Form::close() }}
	</div>
</div>
</div>
@endsection
