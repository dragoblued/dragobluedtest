@if(!isset($item))
   <th>Type</th>
   <th>Name</th>
   <th>Video converting status</th>
   <th></th>
@else
   <td>{{ $item->type }}</td>
   <td>{{ $item->name }}</td>
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
   @else
      <td></td>
   @endif
   @if($item->type === 'image')
      <td>
         @if($item->url)
            <a class="js-fancy" rel="group" href="{{ asset($item->url) }}">
               <img class="preview__img" src="{{ asset(str_replace('.', '_min.', $item->url)).'?random='.microtime(true) }}" alt="{{ $item->name }}">
            </a>
         @endif
      </td>
   @else
      <td>
         @if($item->converted === 1 && $item->url)
            <video class="preview__img" controls preload="none">
               <source src="{{ asset($item->available_formats ? str_replace('.', '_'.$item->available_formats[0].'.', $item->url) : $item->url) }}" type="video/mp4">
            </video>
         @elseif($item->converted === 2 && $item->url)
            <video data-src="{{ asset($item->available_formats ? str_replace('.', '_0.', $item->url) : $item->url) }}"
                   class="preview__img video-hls" controls poster="{{ asset($item->poster_url ? str_replace('.', '_min.', $item->poster_url) : null) }}">
            </video>
         @endif
      </td>
   @endif
@endif

@section('js')
   <script>
      const token = document.getElementsByName('_token')[0].value;
      const fastUpdateUrl = "{{config('app.app_url')}}" +`/admin/gallery-reconvert-mp4-to-hls/`;

      const mp4ToHls = (id) => {
         const statusCell = document.querySelector(`[data-convert-id="${id}"]`);
         console.log(id, statusCell);
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

      window.onload = () => {
         setHlsVideos('video-hls');
      }
   </script>
@endsection
