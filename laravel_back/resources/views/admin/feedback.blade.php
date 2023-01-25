@if(!isset($item))
	<th>Name</th>
	<th>Message</th>
	<th>E-mail</th>
	<th>Phone</th>
	<th>Sent from</th>
    <th>Created at</th>
@else
	<td>{{ $item->name }}</td>
	<td>{{ $item->text }}</td>
    <td><a href="mailto:{{ $item->email }}">{{ $item->email }}</a></td>
    <td><a href="tel:{{ $item->phone }}">{{ $item->phone }}</a></td>
    <td><a href="{{ $item->url_from }}" target="_blank">{{ $item->url_from }}</a></td>
	<td>{{ $item->created_at->format('Y F d, H:i') }}</td>
@endif
