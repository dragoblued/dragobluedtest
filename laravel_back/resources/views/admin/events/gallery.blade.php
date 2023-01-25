<div class="row">
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
@push('scripts')
   <script src="https://cdn.jsdelivr.net/gh/RubaXa/Sortable/Sortable.min.js"></script>
   <script src="{{ asset('js/inc/gallery.js') }}"></script>
   <script>
      setGalleryVariables({!! $gallery ?? '[]' !!});
   </script>
@endpush
