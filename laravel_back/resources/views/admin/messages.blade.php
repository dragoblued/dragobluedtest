@if(!isset($item))
   <th>User</th>
   <th>Message</th>
   <th>Lesson</th>
   <th>Created at</th>
@else
   <td data-sort="{{$item->user ? ($item->user->name ? $item->user->name : $item->user->email) : null}}"
       data-search="{{$item->user ? ($item->user->name ? $item->user->name : $item->user->email) : null}}">
      <span type="button" class="color-gold cursor-pointer" data-id="{{$item->user->id}}"
            onclick="showUserInfo({{$item->user->id}})"
      >{{ $item->user ? ($item->user->name ? $item->user->name : $item->user->email) : null }}</span>
   </td>
   <td>{{ $item->text }}</td>
   <td data-sort="{{$item->room->lesson->title ?? null}}"
       data-search="{{$item->room->lesson->title ?? null}}">
      @isset($item->room->lesson)
         <a href="{{ config('app.site_url').'/video-courses/'.$item->room->lesson->topic->course->route.'/topics/'.$item->room->lesson->topic->route.'/lessons/'.$item->room->lesson->route.'?fromAdmin=1' }}" target="_blank"
         >{{ $item->room->lesson->title }}</a>
      @endisset
   </td>
   <td>{{ $item->created_at->format('Y F d, H:i') }}</td>
@endif
@section('js')
   <script>
      const fillModal = (user) => {
         console.log(user);
         const modalBody = document.getElementById('exampleModalCenterBody');
         modalBody.innerHTML = '';

         if (user.avatar_url) {
            const img = document.createElement('img');
            img.src = '/'+user.avatar_url;
            img.classList.add('modal-card__avatar');
            modalBody.appendChild(img);
         }
         if (user.name) {
            const p = document.createElement('p');
            p.classList.add('modal-card__line', 'mx-auto');
            p.innerHTML = '<span class="font-weight-bold">Name: </span>' + '<span class="ml-3">'+user.name+'</span>';
            modalBody.appendChild(p);
         }
         if (user.login) {
            const p = document.createElement('p');
            p.classList.add('modal-card__line', 'mx-auto');
            p.innerHTML = '<span class="font-weight-bold">Login: </span>' + '<span class="ml-3">'+user.login+'</span>';
            modalBody.appendChild(p);
         }
         if (user.email) {
            const p = document.createElement('p');
            p.classList.add('modal-card__line', 'mx-auto');
            p.innerHTML = '<span class="font-weight-bold">Email: </span>' + '<a class="ml-3" href="mailto:'+user.email+'">'+user.email+'</a>';
            modalBody.appendChild(p);
         }
         if (user.role) {
            const p = document.createElement('p');
            p.classList.add('modal-card__line', 'mx-auto');
            p.innerHTML = '<span class="font-weight-bold">Role: </span>' + '<span class="ml-3">'+user.role.name+'</span>';
            modalBody.appendChild(p);
         }
         if (user.phone) {
            const p = document.createElement('p');
            p.classList.add('modal-card__line', 'mx-auto');
            p.innerHTML = '<span class="font-weight-bold">Phone number: </span>' + '<a class="ml-3 phone-mask" href="tel:'+user.phone+'">'+user.phone+'</a>';
            modalBody.appendChild(p);
         }
      }
      const showUserInfo = (id) => {
         console.log(id);
         $.ajax({
            type:'GET',
            url:`/admin/users/${id}`,
            success: function(data) {
               fillModal(data);
               $('#exampleModalCenter').modal('show');
            },
            error: function(error) {
               console.log(error);
            }
         });
      }
   </script>
@endsection
