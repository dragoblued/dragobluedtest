<div class="js-files__list files">
   @if(isset($item))
      @if(isset($line->items))
         @foreach($line->items as $file)
            <div class="files__item" id="{{$key}}_item">
               <button class="files__remove-btn" type="button" onclick="markFilesAsRemoved('{{$key}}')">&times;</button>
               @switch(true)
                  @case(str_starts_with($file->mime, 'image'))
                  <a href="{{ asset($file->src) }}?{{ rand(0, 100) }}" class="files__link js-fancy" target="_blank">
                     <img src="{{ asset($file->src) }}?{{ rand(0, 100) }}" alt="" class="files__img">
                     <div class="files__mime">{{ $file->mime }}</div>
                  </a>
                  @break
                  @case(str_starts_with($file->mime, 'video'))
                  <video class="files__video" controls>
                     <source src="{{ asset($file->formats ? str_replace('.', '_'.$file->formats[0].'.', $file->src) : $file->src) }}"
                             type="{{$file->mime}}">
                  </video>
                  @break
                  @case($file->mime === 'application/x-mpegURL')
                  <video class="files__video video-hls" controls
                     data-src="{{ asset($file->formats ? str_replace('.', '_'.$file->formats[0].'.', $file->src) : $file->src) }}">
                  </video>
                  @break
                  @case($file->mime === 'application/pdf')
                  <div class="files__ico">
                     <i class="far fa-file-pdf"></i>
                  </div>
                  <div class="files__mime">{{ $file->mime }}</div>
                  @break
                  @default
                  <a href="{{ asset($file->src) }}" class="files__link js-fancy" target="_blank">
                     <img src="{{ asset($file->src) }}?{{ rand(0, 100) }}" alt="" class="files__preview">
                  </a>
                  @break
               @endswitch
            </div>
         @endforeach
         @if(count($line->items) < 1)
            <span class="files__empty badge badge-light">Not uploaded</span>
         @endif
      @else
         <span class="files__empty badge badge-light">Not uploaded</span>
      @endif
   @else
      <span class="files__empty badge badge-light">Not uploaded</span>
   @endif
</div>

@push('scripts')
   <script>
      const markFilesAsRemoved = (name) => {
         console.log(name);
         const delCheck = document.getElementById(name+'_delete');
         const delItem = document.getElementById(name+'_item');
         if (delCheck) {
            delCheck.setAttribute('checked', 'checked');
            if (delItem) {
               delItem.remove();
            }
         }
      }
   </script>
@endpush
