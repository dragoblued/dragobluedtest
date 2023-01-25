@if(!isset($item))
    <th>Title</th>
    <th>Langs</th>
    <th>Order</th>
    <th title="Topics count">Topics*</th>
    <th title="Lessons count">Lessons*</th>
    <th>Total lessons duration</th>
    <th>Actual price</th>
    <th>Discount price</th>
    <th title="Course page visits count">Views*</th>
    <th title="Purchases count">Purchases*</th>
    <th>Poster</th>
    <th>Status</th>
@else
    <td>
        <a href="{{ 'https://aplinkosaugostest.acruxcsdev.xyz/video-courses/'.$item->route.'?fromAdmin=1' }}"
           target="_blank"
        >{{$item->title }}</a>
    </td>
    <td>{{ $item->langStr }}</td>
    <td>{{ $item->order }}</td>
    <td>{{ $item->topics_count }}</td>
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
    <td>
       <div class="btn-group">
         <span type="button"
               class="badge {{ $item->status === 'published' ? 'badge-info' : ($item->status === 'editing' ? 'badge-warning' : ($item->status === 'coming-soon' ? 'badge-dark' : 'badge-secondary'))}} color-white dropdown-toggle"
               data-status-id="{{$item->id}}"
               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{ $item->status ? $item->status : 'no status' }}</span>
          <div class="dropdown-menu dropdown-menu-right">
             <button class="dropdown-item" onclick="changeStatus({{$item->id}}, 'editing')">Editing</button>
             <button class="dropdown-item" onclick="changeStatus({{$item->id}}, 'published')">Published</button>
             <button class="dropdown-item" onclick="changeStatus({{$item->id}}, 'coming-soon')">Coming-soon</button>
          </div>
       </div>
    </td>
@endif

@section('js')
   <script>
      const token = document.getElementsByName('_token')[0].value;
      const fastUpdateUrl = "{{config('app.app_url')}}" +`/admin/courses-fast-update/`;

      const changeStatus = (id, status) => {
         console.log(id, status);
         if (id && status) {
            const body = {"status": status};
            $.ajax({
               url: fastUpdateUrl + id,
               type: 'PUT',
               data: body,
               headers: {
                  'X-CSRF-TOKEN': token
               },
               success: function(data){
                  $('.alert').remove();
                  $('.content__header').after('<div class="alert">'+data+'</div>');
                  const badge = document.querySelector(`[data-status-id="${id}"]`);
                  if (status === 'editing') {
                     badge.classList.remove('badge-info','badge-dark','badge-secondary');
                     badge.classList.add('badge-warning');
                     badge.innerText = 'editing';
                  } else if (status === 'published') {
                     badge.classList.remove('badge-warning','badge-dark','badge-secondary');
                     badge.classList.add('badge-info');
                     badge.innerText = 'published';
                  } else if (status === 'coming-soon') {
                     badge.classList.remove('badge-warning','badge-info','badge-secondary');
                     badge.classList.add('badge-dark');
                     badge.innerText = 'coming-soon';
                  }
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
