<div class="row">
	<div class="col-6 col-md-4">
      <label class="form__line">Actual price</label>
		<div class="form__line text-nowrap">
			<input type="number" step="1" min="0" name="actual_price" class="w-75" value="{{ isset($item) ? $item->actual_price : ''}}">
			<span>{{ $currency_icon ?? ''}}</span>
		</div>
	</div>
	<div class="col-6 col-md-4">
		<label class="form__line">Discount price</label>
		<div class="form__line text-nowrap">
			<input type="number" step="1" min="0" name="discount_price" class="w-75" value="{{ isset($item) ? $item->discount_price : ''}}">
			<span>{{ $currency_icon ?? ''}}</span>
		</div>
	</div>
</div>
