@if(!isset($item))
	<th>Active</th>
	<th>Name</th>
	<th>Sector</th>
	<th>Phone</th>
	<th>E-mail</th>
	<th>Groups</th>
@else
	<td>{{ $item->active_text }}</td>
	<td>{{ $item->name }}</td>
	<td>@foreach($item->sector as $sector){{ $sector }}&ensp;@endforeach</td>
	<td>{{ $item->phone }}</td>
	<td>{{ $item->email }}</td>
	<td>
		@isset($item['group'])
			@foreach($item->groupes()->get() as $group)
				{{ $group->name }}
			@endforeach
		@endisset
	</td>
@endif
