@if(!isset($item))
   <th>Code</th>
   <th>Discount</th>
   <th>Start at</th>
   <th>End at</th>
   <th>Subject item</th>
   <th>Usage limit</th>
   <th>Usage count</th>
@else
   <td>{{ $item->code }}</td>
   <td>{{ $item->discount.' '.($item->discount_type === 'percent' ? '%' : $currencySign)  }}</td>
   <td>{{ date('Y F d', strtotime($item->start_at)) }}</td>
   <td>{{ date('Y F d', strtotime($item->end_at)) }}</td>
   <td>
      @if($item->subject)
         @switch($item->subject_type)
            @case('App\Course')
            <a href="/admin/courses?highlight={{$item->subject->id}}">{{$item->subject->title}}</a>
            @break
            @case('App\Topic')
            <a href="/admin/topics?highlight={{$item->subject->id}}">{{$item->subject->title}}</a>
            @break
            @case('App\Event')
            <a href="/admin/events?highlight={{$item->subject->id}}">{{$item->subject->title}}</a>
            @break
         @endswitch
      @endif
   </td>
   <td>{{ $item->usage_limit }}</td>
   <td>{{ $item->usage_count }}</td>
@endif
