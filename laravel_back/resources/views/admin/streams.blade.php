@if(!isset($item))
   <th>Title</th>
   <th>Stream link</th>
   <th>Language</th>
   <th>Poster</th>
   <th>Created at</th>
@else
   <td data-sort="{{ $item->title }}" data-search="{{ $item->title }}">
      <a href="/admin/streams/{{$item->id}}"
      >{!! $item->title.($item->is_free ? ' <mark style="color: brown;">*Free</mark>' : '') !!}</a>
   </td>

   <td data-sort="{{ $item->title }}" data-search="{{ $item->title }}">
      <span id="link-{{$item->id}}" class="align-middle">{{config('app.site_url')}}/stream/{{$item->name}}?key={{$item->key}}</span>
      <button class="btn btn-dark btn-sm font-weight-bold fz-0_6rem" type="button" data-target="link-{{$item->id}}"
              onclick="copyToClipboard('link-{{$item->id}}')">copy</button>
   </td>

   <td data-sort="{{ $item->lang }}" data-search="{{ $item->lang }}">{{ $item->lang }}</td>

   <td>
   @if($item->poster_url)
      <a class="js-fancy" rel="group" href="{{ asset($item->poster_url).'?rnd='.microtime(true) }}" target="_blank">
         <img class="preview__img preview__img_small" alt="Lesson Poster"
              src="{{ asset(str_replace('.', '_min.', $item->poster_url)).'?rnd='.microtime(true) }}">
      </a>
   @endif
   </td>
   <td>{{ $item->created_at }}</td>
@endif

@section('js')
   <script src="{{ asset('js/inc/copy-to-clipboard.js') }}"></script>
@endsection
