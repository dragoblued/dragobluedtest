@if(!isset($item))
   <th>Title</th>
@else
   <td>{{ $item->title }}</td>
@endif
