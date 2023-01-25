@extends('layouts.email')

@section('content')
	<h4>{{ $title }}</h4>
	<p>{{ $description }}</p>
	<table style="border: 1px dashed black">
		<tr>
			<td style="padding-right: 60px">Order</td>
			<td>{{ $order }}</td>
		</tr>
		<tr>
			<td>Product</td>
			<td>{{ $product }}</h4></td>
		</tr>
		<tr>
			<td>Price</td>
			<td>{{ $price.''. $currency }}</h4></td>
		</tr>
		<tr>
			<td>Total</td>
			<td>{{ $total.''. $currency }}</h4></td>
		</tr>
		<tr>
			<td>Date</td>
			<td>{{ $date }}</h4></td>
		</tr>
	</table>
@endsection