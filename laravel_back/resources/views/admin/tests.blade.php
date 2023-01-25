@if(!isset($item))
    <th>Title</th>
    <th>Course</th>
    <th>Duration</th>
    <th>Total mark</th>
    <th>Status</th>
    <th>Created at</th>
@else
    <td>{{ $item->title }}</td>
    <td>
        @if(!is_null($item->course))
            <a href="{{ config('app.app_url').'/admin/courses?highlight='.$item->course->id }}"
               target="_self"
            >{{ $item->course->title }}</a>
        @endif
    </td>
    <td>{{ $item->duration }}</td>
    <td>{{ $item->total_mark }}</td>
    <td>{{ $item->status }}</td>
    <td>{{ $item->created_at->format('Y M d') }}</td>
@endif
