@if(!isset($item))
    <th>Titile</th>
    <th>Test</th>
    <th>Type</th>
    <th>Mark</th>
    <th>Created at</th>
@else
    <td>{!! $item->title !!}</td>
    <td>
        @if(!is_null($item->test))
            <a href="{{ config('app.app_url').'/admin/tests?highlight='.$item->test->id }}"
               target="_self"
            >{{ $item->test->title }}</a>
        @endif
    </td>
    <td>{{ $item->type }}</td>
    <td>{{ $item->mark }}</td>
    <td>{{ $item->created_at->format('Y M d') }}</td>
@endif
