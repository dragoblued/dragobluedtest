@if(!isset($item))
    <th>Title</th>
    <th>Course</th>
    <th>Langs</th>
    <th>Order</th>
    <th title="Lessons count">Lessons*</th>
    <th>Total lesson duration</th>
    <th>Actual price</th>
    <th>Discount price</th>
    <th title="Topic page visits count">Views*</th>
    <th title="Purchases count">Purchases*</th>
    <th>Poster</th>
@else
    <td>
        <a 
           target="_blank"
        >{{$item->title }}</a>
    </td>
    <td>
        @if(!is_null($item->course))
            <a href="{{ config('app.app_url').'/admin/courses?highlight='.$item->course->id }}"
               target="_self"
            >{{ $item->course->title }}</a>
        @endif
    </td>
    <td>{{ $item->langStr }}</td>
    <td>{{ $item->order }}</td>
    <td>{{ $item->lessons_count }}</td>
    <td>{{ gmdate("H:i:s", $item->total_lessons_duration) }}</td>
    <td>{{ $item->actual_price }}</td>
    <td>{{ $item->discount_price }}</td>
    <td>{{ $item->view_count }}</td>
    <td>{{ $item->purchase_count }}</td>
    <td>
       @if($item->poster_url)
          <a class="js-fancy" rel="group" href="{{ asset($item->poster_url) }}">
             <img class="preview__img preview__img_small"
                  src="{{ asset(str_replace('.', '_min.', $item->poster_url)).'?random='.microtime(true) }}"
                  alt="Course Poster">
          </a>
       @endif
    </td>
@endif
