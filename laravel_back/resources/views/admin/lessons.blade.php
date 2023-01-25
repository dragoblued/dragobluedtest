@if(!isset($item))
   <th>Course</th>
   <th>Title</th>
   <th>Topic</th>
   <th>Order</th>
   <th>Duration</th>
   <th>Converting status</th>
   <th title="Views count">Views*</th>
   <th></th>
@else
   <td data-sort="{{ isset($item->topic->course) ? $item->topic->course->title : null }}"
       data-search="{{ isset($item->topic->course) ? $item->topic->course->title : null }}">
      @isset($item->topic->course)
         <a href="{{ config('app.app_url').'/admin/courses?highlight='.$item->topic->course->id }}"
            target="_self"
         >{{ $item->topic->course->title }}</a>
      @endif
   </td>
   <td data-sort="{{ $item->title }}" data-search="{{ $item->title }}">
      @if(isset($item->topic->course))
         <a href="{{ config('app.site_url').'/video-courses/'.$item->topic->course->route.'/topics/'.$item->topic->route.'/lessons/'.$item->route.'?fromAdmin=1' }}"
            target="_blank"
         >{!! $item->title.($item->is_free ? ' <mark style="color: brown;">*Free</mark>' : '') !!}</a>
      @else
         {!! $item->title.($item->is_free ? ' <mark style="color: brown;">*Free</mark>' : '') !!}
      @endif
   </td>
   <td data-sort="{{ isset($item->topic) ? $item->topic->title : null }}"
       data-search="{{ isset($item->topic) ? $item->topic->title : null }}">
      @isset($item->topic)
         <a href="{{ config('app.app_url').'/admin/topics?highlight='.$item->topic->id }}"
            target="_self"
         >{{ $item->topic->title }}</a>
      @endisset
   </td>
   <td data-sort="{{ $item->order }}" data-search="{{ $item->order }}">{{ $item->order }}</td>
   <td data-sort="{{ $item->video_duration }}">{{ gmdate("H:i:s", $item->video_duration) }}</td>

   @if($item->converted === 0)
      <td data-convert-id="{{$item->id}}" data-sort="No video" data-search="No video">No video</td>
   @elseif($item->converted === 1)
      <td data-convert-id="{{$item->id}}" data-sort="Finished" data-search="Finished">
         <span class="align-middle mr-2">Finished</span>
         <button class="btn btn-sm btn-dark fz-0_6rem align-middle" onclick="mp4ToHls({{$item->id}})">Reconvert to HLS</button>
   @elseif($item->converted === 2)
      <td data-convert-id="{{$item->id}}" data-sort="Finished" data-search="Finished">Finished</td>
   @elseif($item->converted === 3)
      <td data-convert-id="{{$item->id}}" data-sort="Converting" data-search="Converting">Converting...</td>
   @elseif($item->converted === 4)
      <td data-convert-id="{{$item->id}}" data-sort="Error" data-search="Error">
         <span class="badge badge-danger" title="Contact developers to get help">Error*</span>
      </td>
   @endif

   <td data-sort="{{ $item->view_count }}">{{ $item->view_count }}</td>
   <td>
      @if($item->poster_url)
         <a class="js-fancy" rel="group" href="{{ asset($item->poster_url).'?rnd='.microtime(true) }}" target="_blank">
            <img class="preview__img preview__img_small" src="{{ asset(str_replace('.', '_min.', $item->poster_url)).'?rnd='.microtime(true) }}"
                 alt="Lesson Poster">
         </a>
      @endif
      {{--        @if($item->converted === 1 && $item->video_url)--}}
      {{--            <video class="preview__img" controls>--}}
      {{--                <source src="{{ asset($item->video_available_formats ? str_replace('.mp4', '_'.$item->video_available_formats[0].'.webm', $item->video_url) : $item->video_url) }}" type="video/webm">--}}
      {{--                <source src="{{ asset($item->video_available_formats ? str_replace('.', '_'.$item->video_available_formats[0].'.', $item->video_url) : $item->video_url) }}" type="video/mp4">--}}
      {{--            </video>--}}
      {{--        @endif--}}
   </td>
@endif

@section('js')
   <script>
      const token = document.getElementsByName('_token')[0].value;
      const fastUpdateUrl = "{{config('app.app_url')}}" +`/admin/lessons-reconvert-mp4-to-hls/`;

      const mp4ToHls = (id) => {
         const statusCell = document.querySelector(`[data-convert-id="${id}"]`);
         if (confirm('Don\'t confirm if you don\'t know what this operation does.') && id && statusCell) {
            console.log(id);
            $.ajax({
               url: fastUpdateUrl + id,
               type: 'GET',
               headers: {
                  'X-CSRF-TOKEN': token
               },
               success: function(data){
                  $('.alert').remove();
                  $('.content__header').after('<div class="alert">'+data+'</div>');
                  statusCell.setAttribute('data-sort', 'Converting');
                  statusCell.setAttribute('data-search', 'Converting');
                  statusCell.innerText = 'Converting...';
                  console.log(data);
               },
               error: function(jqXHR, textStatus, errorThrown){
                  $('.alert').remove();
                  $('.content__header').after('<div class="alert">Error occurred: ' + errorThrown+'</div>');
                  console.log("ERROR: " + textStatus + ", " + errorThrown);
                  console.log(jqXHR);
               },
            });
         }
      }
   </script>
@endsection
