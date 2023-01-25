@if(!isset($item))
   <th>Title</th>
   <th>Order</th>
   <th title="Duration in days">Days</th>
   <th>Langs</th>
   <th>Actual price</th>
   <th>Discount price</th>
   <th>Model is viewed</th>
   <th title="Course page visits count">Views</th>
   <th title="Bought tickets count">Purchases</th>
   <th>Poster</th>
   <th>Status</th>
@else
   <td>
      <a href="{{ config('app.site_url').'/live-courses/'.$item->route.'?fromAdmin=1' }}"
         target="_blank"
      >{{$item->title }}</a>
   </td>
   <td>{{ $item->order }}</td>
   <td>{{ $item->duration }}</td>
   <td>{{ $item->langStr }}</td>
   <td>{{ $item->actual_price }}</td>
   <td>{{ $item->discount_price }}</td>
   <td class="text-center">
      <div class="d-inline-block custom-control custom-checkbox">
         <input type="checkbox" class="custom-control-input event__is-model" id="{{$item->id}}"
         {{ $item->is_model_visible === 1 ? 'checked=checked' : null}}">
         <label class="custom-control-label cursor-pointer" for="{{$item->id}}"></label>
      </div>
   </td>
   <td>{{ $item->view_count }}</td>
   <td>{{ $item->bought_tickets_count }}</td>
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
               class="badge {{ $item->status === 'published' ? 'badge-info' : ($item->status === 'editing' ? 'badge-warning' : 'badge-secondary')}} color-white dropdown-toggle"
               data-status-id="{{$item->id}}"
               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{ $item->status ? $item->status : 'no status' }}</span>
         <div class="dropdown-menu dropdown-menu-right">
            <button class="dropdown-item" onclick="changeStatus({{$item->id}}, 'editing')">Editing</button>
            <button class="dropdown-item" onclick="changeStatus({{$item->id}}, 'published')">Published</button>
         </div>
      </div>
   </td>
@endif

@section('js')
   <script>
      const boxes = Array.from(document.getElementsByClassName('event__is-model'));
      const token = document.getElementsByName('_token')[0].value;
      const fastUpdateUrl = "{{config('app.app_url')}}" +`/admin/events-fast-update/`;

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
                     badge.classList.remove('badge-info','badge-secondary');
                     badge.classList.add('badge-warning');
                     badge.innerText = 'editing';
                  } else if (status === 'published') {
                     badge.classList.remove('badge-warning','badge-secondary');
                     badge.classList.add('badge-info');
                     badge.innerText = 'published';
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

      const onCheckboxChange = (event) => {
         const id = event.target.getAttribute('id');
         const value = event.target.checked ? 1 : 0;
         const body = {"is_model_visible": value};
         console.log(id, value);
         if (id) {
            $.ajax({
               url: fastUpdateUrl  + id,
               type: 'PUT',
               data: body,
               headers: {
                  'X-CSRF-TOKEN': token
               },
               success: function(data){
                  $('.alert').remove();
                  $('.content__header').after('<div class="alert">'+data+'</div>');
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
      boxes.forEach((checkBox) => {
         checkBox.addEventListener('change', onCheckboxChange);
      });
   </script>
@endsection
