@if(!isset($item))
	<th>Right</th>
	<th>Description</th>
@else
	<td>{{ $item->name }}</td>
	<td>{{ $item->description }}</td>
@endif
