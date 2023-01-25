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
   <div class="row mt-5">
      <div class="col-12" >
         <div class="form-group">
            <label class="d-block mb-2">Video in the middle</label>
            <input type="hidden" name="middle_video_url" id="middle_video_url" value="{{$middleVideoItem->id ?? null}}">
            <button class="btn btn-light font-weight-bold"
                    onclick="showGallery()" type="button">Select video from the gallery</button>
         </div>
         <div id="videoContainer">
            @if($middleVideoItem)
               <div class="gallery-item-wrap">
                  <video controls data-src="/{{str_replace('.m3u8', "_0.m3u8", $middleVideoItem->url)}}" class="gallery-item video-hls"></video>
               </div>
            @endif
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
               <button type="button" class="btn btn-primary font-weight-bold color-white"
                       data-dismiss="modal" onclick="selectVideo()">Select</button>
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
   <script>
      let selectedItemId = null;
      let selectedItem = null;
      let items = [];
      const getVideoSelectTemplate = (item) => {
         return `<button class="gallery-item-select-btn" type="button" onclick="selectItem(${item.id})"></button>
         <video controls data-src="/${item.url.replace('.m3u8', `_0.m3u8`)}" class="gallery-item video-hls"></video>`;
      }
      const getVideoTemplate = (item) => {
         return `<video controls data-src="/${item.url.replace('.m3u8', `_0.m3u8`)}" class="gallery-item video-hls"></video>`;
      }

      const cleanSelect = () => {
         const blocks = Array.from(document.getElementsByClassName('gallery-item-wrap'));
         blocks.forEach((block) => block.classList.remove('selected'));
         selectedItemId = null;
      }

      const selectItem = (id) => {
         cleanSelect();
         const block = document.getElementById('gallery-item-'+id);
         if (block) {
            block.classList.add('selected');
            selectedItemId = id;
         }
      }

      const appendItemTo = (item, block, isSelect = false) => {
         if (item.url && item.available_formats instanceof Array) {
            const el = document.createElement('div');
            el.setAttribute('id', 'gallery-item-'+item.id);
            el.classList.add('gallery-item-wrap');
            el.innerHTML = isSelect === true ? getVideoSelectTemplate(item) : getVideoTemplate(item);
            block.append(el);
         }
      }
      const fillModal = (items) => {
         console.log(items);
         const modalBody = document.getElementById('exampleModalCenterBody');
         modalBody.innerHTML = '';
         items.forEach(item => {
            appendItemTo(item, modalBody, true);
         });
         setHlsVideos('video-hls');
      }
      const showGallery = () => {
         $.ajax({
            type:'GET',
            url:`/admin/gallery?only=video`,
            success: function(data) {
               items = data;
               fillModal(data);
               setHlsVideos('video-hls');
               $('#exampleModalCenter').modal('show');
            },
            error: function(error) {
               console.log(error);
            }
         });
      }

      const selectVideo = () => {
         console.log(selectedItemId);
         selectedItem = items.find(el => el.id === selectedItemId);
         if (selectedItem) {
            const container = document.getElementById('videoContainer');
            container.innerHTML = '';
            appendItemTo(selectedItem, container);
            setHlsVideos('video-hls');
            document.getElementById('middle_video_url').value = selectedItemId;
         }
      }

      window.onload = () => {
         setHlsVideos('video-hls');
      }
   </script>
@endsection
