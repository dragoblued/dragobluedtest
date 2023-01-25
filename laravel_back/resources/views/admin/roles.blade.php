@if(!isset($item))
	<th>Role</th>
	<th>List of rights</th>
@else
	<td>{{ $item->name }}</td>
	<td>
		@isset($item['permissions'])
			@foreach($item->permissions()->get() as $permission)
				{{ $permission->name }}
			@endforeach
		@endisset
	</td>
@endif
