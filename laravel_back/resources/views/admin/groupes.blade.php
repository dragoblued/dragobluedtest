@if(!isset($item))
	<th>Name</th>
	<th>Description</th>
@else
	<td>{{ $item->name }}</td>
	<td>{!! $item->description !!}</td>
@endif
