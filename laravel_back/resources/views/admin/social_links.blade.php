@if(!isset($item))
	<th>Name</th>
	<th>Url</th>
	<th>Icon</th>
	@else
	<td>{{ $item->name }}</td>
	<td>{!! $item->url !!}</td>
	<td>{!! $item->icon !!}</td>
@endif
