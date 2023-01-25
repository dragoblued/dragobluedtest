@extends('layouts.admin')
@section('content')
   @if($errors->all())
      <div class="errors">
         @foreach($errors->all() as $error)
            <div class="errors__item">
               {!! $error !!}
            </div>
         @endforeach
      </div>
   @endif
   {{ Form::model(null, [
      'route' => [ "{$page->route}.update", $item->id ],
      'files' => true,
      'class' => 'form container-fluid'
   ]) }}
   {{ method_field('PUT') }}
   <div class="row mt-4">
      <div class="col-12">
         <div class="form-group">
            <label class="d-block mb-2">Header Video</label>
            <input type="hidden" name="header_video_url" id="header_video_url" value="{{$headerVideoItem->id ?? null}}">
            <button class="btn btn-light font-weight-bold"
                    onclick="oneShowGallery()" type="button">Select video from the gallery</button>
         </div>
         <div id="videoContainer">
            @if($headerVideoItem)
               <div class="gallery-item-wrap">
                  <video controls data-src="/{{str_replace('.m3u8', "_0.m3u8", $headerVideoItem->url)}}" class="gallery-item video-hls"></video>
               </div>
            @endif
         </div>
      </div>
      <div class="col-12">
         <div class="form-group">
            <label class="d-block mb-2" for="description">Introduction text</label>
            <textarea name="description" id="description" rows="2" class="wysiwyg js-wysiwyg"
                      data-uploader="{{route('admin.uploader', ['folder' => 'media/wisiwyg/pages/'])}}"
                      data-filebrowser="{{route('admin.browser', ['folder' => 'media/wisiwyg/pages/'])}}"
            >{{$item->content['description'] ?? ''}}</textarea>
         </div>
      </div>
      <div class="col-12">
         <div class="form-group">
            <label class="d-block mb-2">Page gallery slider</label>
            <input type="hidden" name="gallery" id="gallery">
            <button class="btn btn-light font-weight-bold"
                    onclick="showGallery()" type="button">Select items from the gallery</button>
         </div>
         <div id="galleryContainerWrap">
            <ul id="galleryContainer"></ul>
         </div>
      </div>
   </div>

   <div class="form__line" style="margin-top: 20px; text-align: center;">
      {{ Form::submit('Save', [ 'class' => 'button' ]) }}
   </div>
   {{ Form::close() }}
   <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
         <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title font-weight-bold fz-1_5rem" id="exampleModalLongTitle">Gallery</h5>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
               </button>
            </div>
            <div id="exampleModalCenterBody" class="modal-body modal-card d-flex flex-wrap justify-content-center">
            </div>
            <div class="modal-footer">
{{--               <button type="button" class="btn btn-dark font-weight-bold color-white mr-auto"--}}
{{--                       onclick="loadMore()">Load more items</button>--}}
               <button type="button" class="btn btn-primary font-weight-bold color-white"
                       data-dismiss="modal" onclick="selectVideo()">Select</button>
               <button type="button" class="btn btn-secondary font-weight-bold"
                       data-dismiss="modal">Close</button>
            </div>
         </div>
      </div>
   </div>
   <div class="modal fade" id="oneVideoModalCenter" tabindex="-1" role="dialog" aria-labelledby="oneVideoModalCenter" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
         <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title font-weight-bold fz-1_5rem" id="exampleModalLongTitle">Gallery</h5>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
               </button>
            </div>
            <div id="oneVideoModalCenterBody" class="modal-body modal-card d-flex flex-wrap justify-content-center">
            </div>
            <div class="modal-footer">
               {{--               <button type="button" class="btn btn-dark font-weight-bold color-white mr-auto"--}}
               {{--                       onclick="loadMore()">Load more items</button>--}}
               <button type="button" class="btn btn-primary font-weight-bold color-white"
                       data-dismiss="modal" onclick="oneSelectVideo()">Select</button>
               <button type="button" class="btn btn-secondary font-weight-bold"
                       data-dismiss="modal">Close</button>
            </div>
         </div>
      </div>
   </div>
@endsection

@section('js')
   <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
   <script src="{{ asset('js/inc/video-hls.js') }}"></script>
   <script src="https://cdn.jsdelivr.net/gh/RubaXa/Sortable/Sortable.min.js"></script>
   <script src="{{ asset('js/inc/gallery.js') }}"></script>
   <script>
      setGalleryVariables({!! $gallery ?? '[]' !!});
   </script>

   <script>
      let oneSelectedItemId = null;
      let oneSelectedItem = null;
      let onlyVideoItems = [];
      const getOneVideoSelectTemplate = (item) => {
         return `<button class="gallery-item-select-btn" type="button" onclick="selectOneItem(${item.id})"></button>
         <video controls data-src="/${item.url.replace('.mp4', `_${item.available_formats[0]}.mp4`)}" class="gallery-item video-hls"></video>`;
      }
      const getOneVideoTemplate = (item) => {
         return `<video controls data-src="/${item.url.replace('.mp4', `_${item.available_formats[0]}.mp4`)}" class="gallery-item video-hls"></video>`;
      }

      const cleanOneSelect = () => {
         const blocks = Array.from(document.getElementsByClassName('gallery-item-wrap'));
         blocks.forEach((block) => block.classList.remove('selected'));
         oneSelectedItemId = null;
      }

      const selectOneItem = (id) => {
         cleanOneSelect();
         const block = document.getElementById('gallery-item-'+id);
         if (block) {
            block.classList.add('selected');
            oneSelectedItemId = id;
         }
      }

      const appendOneItemTo = (item, block, isSelect = false) => {
         if (item.url && item.available_formats instanceof Array) {
            const el = document.createElement('div');
            el.setAttribute('id', 'gallery-item-'+item.id);
            el.classList.add('gallery-item-wrap');
            el.innerHTML = isSelect === true ? getOneVideoSelectTemplate(item) : getOneVideoTemplate(item);
            block.append(el);
         }
      }
      const oneFillModal = (items) => {
         console.log(items);
         const modalBody = document.getElementById('oneVideoModalCenterBody');
         modalBody.innerHTML = '';
         items.forEach(item => {
            appendOneItemTo(item, modalBody, true);
         });
         setHlsVideos('video-hls');
      }
      const oneShowGallery = () => {
         $.ajax({
            type:'GET',
            url:`/admin/gallery?only=video`,
            success: function(data) {
               onlyVideoItems = data;
               oneFillModal(data);
               setHlsVideos('video-hls');
               $('#oneVideoModalCenter').modal('show');
            },
            error: function(error) {
               console.log(error);
            }
         });
      }

      const oneSelectVideo = () => {
         console.log(oneSelectedItemId);
         oneSelectedItem = onlyVideoItems.find(el => el.id === oneSelectedItemId);
         if (oneSelectedItem) {
            const container = document.getElementById('videoContainer');
            container.innerHTML = '';
            appendOneItemTo(oneSelectedItem, container);
            setHlsVideos('video-hls');
            document.getElementById('header_video_url').value = oneSelectedItemId;
         }
      }

      window.onload = () => {
         setHlsVideos('video-hls');
      }
   </script>
@endsection
