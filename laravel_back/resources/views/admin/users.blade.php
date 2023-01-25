@section('link')
   <link rel="stylesheet" href="{{ asset('css/inc/tree.css') }}">
   <link rel="stylesheet" href="{{ asset('css/inc/user-device.css') }}">
@endsection
@if(!isset($item))
   <th>Avatar</th>
   <th>Active</th>
   <th>Login</th>
   <th>Name</th>
   <th>E-mail</th>
   <th>Phone</th>
   <th>Role</th>
   <th>Devices</th>
   <th>Created at</th>
   <th></th>
@else
   <td class="text-center">
      @isset($item->avatar_url)
         <a href="{{ asset($item->avatar_url) }}?{{ rand(0, 100) }}" class="js-fancy" target="_blank">
            <img src="{{asset(str_replace('.', '_min.', $item->avatar_url))}}?{{ rand(0, 100) }}" height="30" width="30" class="bdrs-50p user-avatar-img" alt="Avatar Img">
         </a>
      @endisset
   </td>
   <td data-sort="{{ $item->active }}"
       data-search="{{ $item->active === 1 ? 'yes' : 'no' }}">
      @if($item->login === 'ROOT')
         <span style="padding: 2px 8px;">{{ $item->active === 1 ? 'yes' : 'no' }}</span>
      @else
         {{ Form::model($item, [
            'route' => [ "{$page->route}.update", $item->id ],
            'files' => true,
            'class' => 'form'
         ]) }}
         {{ method_field('PUT') }}
         {{ Form::select('active', ['no', 'yes'], $item->active ?? [0], [
            'class' => 'active-select',
            'data-search' => 'true'
         ]) }}
         {{ Form::close() }}
      @endif
   </td>
   <td data-sort="{{ $item->login }}"
       data-search="{{ $item->login }}"
   >{{ $item->login }}</td>
   <td data-sort="{{ $item->login }}"
       data-search="{{ $item->login }}"
   >{{ $item->name.' '.$item->surname.' '.$item->middle_name }}</td>
   <td data-sort="{{ $item->email }}"
       data-search="{{ $item->email }}"
   ><a href="mailto:{{ $item->email }}">{{ $item->email }}</a></td>
   <td data-sort="{{ $item->phone }}"
       data-search="{{ $item->phone }}"
   ><a class="phone-mask" href="tel:{{ $item->phone }}">{{ $item->phone }}</a></td>
   <td data-sort="{{ isset($item->role) ? $item->role->name : null }}"
       data-search="{{ isset($item->role) ? $item->role->name : null }}"
   >
      @isset($item->role)
         {{ $item->role->name }}
      @endisset
   </td>
   <td id="user-devices-{{$item->id}}">
      @if(is_array($item->device_ids))
         @foreach($item->device_ids as $idx => $value)
            <span class="user-device" data-user-id="{{$item->id}}" data-device-idx="{{$idx}}"
                  id="device-{{$item->id}}-{{$idx}}" onclick="removeDevice(event, {{$item->id}})">
               <img src="{{asset('media/img/smartphone.svg')}}" height="25" alt="Smartphone Icon" class="user-device__icon">
               <i class="fa fa-times user-device__delete-icon" ></i>
            </span>
         @endforeach
      @endif
   </td>
   <td data-sort="{{ $item->created_at }}"
   >{{ $item->created_at }}</td>
   <td class="list__function list__function_actions">
      <div class="btn-group">
         <button type="button" class="btn btn-dark btn-sm px-3 dropdown-toggle"
                 data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
         >Actions</button>
         <div class="dropdown-menu dropdown-menu-right">
            <a class="dropdown-item" href="{{ route($page->route.'.edit', $item->id) }}">
               <i class="fa fa-edit mr-1 align-middle fz-0_8rem text-primary"></i>
               <span class="align-middle">Edit</span>
            </a>
            <a class="dropdown-item js-delete" href="{{ route($page->route.'.destroy', $item->id) }}">
               <i class="fa fa-trash-alt mr-1 align-middle fz-0_8rem text-primary"></i>
               <span class="align-middle">Delete</span>
            </a>
            <span class="dropdown-item cursor-pointer" onclick="showUserInfo({{$item->id}}, false)">
               <i class="fas fa-tasks mr-1 align-middle text-primary"></i>
               <span class="align-middle">Show Progress</span>
            </span>
         </div>
      </div>
   </td>
@endif
@section('js')
   <script src="{{ asset('js/inc/show-user-info.js') }}"></script>
   <script>
      const freshIndexes = (userId) => {
         [...document.getElementById('user-devices-'+userId).children].forEach((deviceBlock, idx) => {
            deviceBlock.setAttribute('data-device-idx', idx);
            deviceBlock.setAttribute('id', `device-${userId}-${idx}`);
         });
      }

      const removeDevice = (event, userId) => {
         console.log(userId);
         const deviceIdx = event.currentTarget.getAttribute('data-device-idx');
         if (confirm('Delete user device?')) {
            fetch(`/admin/users/${userId}/remove-device/${deviceIdx}`, {
               method: 'GET',
               headers: {
                  'Accept': 'application/json',
                  'Content-Type': 'application/json',
                  'X-Requested-With': 'XMLHttpRequest',
                  'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
               }
            }).then(response => response.json()).then(data => {
               console.log(data);
               document.getElementById(`device-${userId}-${deviceIdx}`).remove();
               freshIndexes(userId);
            }).catch((error) => {
               console.error('Error:', error);
            });
         }
      }

      $('.active-select').change(function() {
         const url = $(this).parent().attr('action');
         const formData = $(this).parent().serialize();
         const token = $(this).siblings('[name="_token"]').val();
         const method = $(this).siblings('[name="_method"]').val();
         $.ajax({
            url: url,
            type: method,
            data: formData,
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
         $(this).parents('.list__item').removeClass('new');
      });
   </script>
@endsection
