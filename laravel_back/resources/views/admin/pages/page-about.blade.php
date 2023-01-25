@extends('layouts.admin')
@section('content')
   <!-- Ошибки валидации -->

   <div class="container-fluid">
      @if(isset($item))

         @if($errors->all())
            <div class="errors">
               @foreach($errors->all() as $error)
                  <div class="errors__item">
                     {!! preg_replace('"The( selected)? (.*?) (field|is|may|must|does|has)(.*)?"', 'The$1 <b>$2</b> $3$4', $error) !!}
                  </div>
               @endforeach
            </div>
         @endif

      <!-- Формирование полей -->
         {{ Form::model(null, [
            'route' => [ "{$page->route}.update", $id ],
            'files' => true,
            'class' => 'form'
         ]) }}
         {{ method_field('PUT') }}

         <div class="row my-3">
            <div class="col-12" >
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

         <div class="row" id="publications">

            @foreach($item as $index => $i)

               <div class="col-12 col-lg-6 form__group">

                  <div class="form__line">
                     <label class="form__line">
                     <span class="form__signature">
                        Theme
                     </span>
                     </label>
                     {{--                  <textarea class="form__theme wysiwyg js-wysiwyg" name="theme_{{++$num}}" placeholder="{{$i['theme'] ?? ''}}" cols="50" rows="10" style="display: none;"></textarea>--}}
                     {{ Form::textarea('theme_'.($i['id'] ?? $index + 1), $i['theme'] ??'',['class' => 'wysiwyg js-wysiwyg form__theme']) }}
                  </div>
                  <div class="form__line">
                     <label class="form__line">
                     <span class="form__signature">
                        Authors
                     </span>
                     </label>
                     {{--                  <input class="form__authors" type="text" name="authors_{{$i['id']}}">--}}
                     {{ Form::text('authors_'.($i['id'] ?? $index + 1), $i['authors'] ?? '',['class' => 'form__authors']) }}
                  </div>
                  <div class="form__line">
                     <label class="form__line">
                     <span class="form__signature">
                        File url
                     </span>
                     </label>

                     <input class="form__file_url jq-file" name="file_url_{{$i['id'] ?? $index + 1}}" type="file" accept=".pdf">
                     @if(isset($i['file_url']))
                        <div class="files__ico">
                           <i class="far fa-file-pdf"></i>
                        </div>
                     @endif
                  </div>
                  <div><button class="btnMinus">-</button></div>

               </div>

            @endforeach

         </div>

         <div style="padding-top: 20px; text-align: center;">
            <button class="btnPlus" style="border: none; background: none;">
               <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-plus-square-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                  <path fill-rule="evenodd" d="M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2zm6.5 4.5a.5.5 0 0 0-1 0v3h-3a.5.5 0 0 0 0 1h3v3a.5.5 0 0 0 1 0v-3h3a.5.5 0 0 0 0-1h-3v-3z"/>
               </svg>
            </button>
         </div>
         <div class="form__line" style="margin-top: 20px; text-align: center;">
            {{ Form::submit('Save', [ 'class' => 'button' ]) }}
         </div>
         {{ Form::close() }}
      @else
         No content available
      @endif
   </div>

   <div class="col-12 col-lg-6 form__group d-none form__empty" id="empty-card">
      <div class="form__line">
         <label class="form__line">
           <span class="form__signature">
              Theme
           </span>
         </label>
         {{--                  <textarea class="form__theme wysiwyg js-wysiwyg" name="theme_{{++$num}}" placeholder="{{$i['theme'] ?? ''}}" cols="50" rows="10" style="display: none;"></textarea>--}}
         <textarea name="theme_" cols="30" rows="10" class="form__theme"></textarea>
      </div>
      <div class="form__line">
         <label class="form__line">
           <span class="form__signature">
              Authors
           </span>
         </label>
         <input class="form__authors" type="text" name="authors_" value="">
      </div>
      <div class="form__line">
         <label class="form__line">
           <span class="form__signature">
              File url
           </span>
         </label>
         <input class="form__file_url jq-file" name="file_url_" type="file" accept=".pdf">
      </div>
      <div><button class="btnMinus">-</button></div>
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
      window.addEventListener('DOMContentLoaded', () => {

         /* btn PLUS */
         const plus = document.querySelector('.btnPlus');

         /* form */
         const emptyForm = document.querySelector('#empty-card');

         /* row */
         const row = document.getElementById('publications');

         /* forms length */
         let maxId = '';
         const forms = document.querySelectorAll('.form__group');
         let formsLength = forms.length;
         if (formsLength === null) {
            maxId = 0;
         } else {
            /* get id */
            const lastForm = forms[formsLength - 2];
            maxId = lastForm.querySelector('.form__authors').getAttribute("name").match(/[^_]+$/)[0];
         }

         /*  --------------- btn PLUS Listener  --------------- */
         let counter = +maxId;
         plus.addEventListener('click', (e) => {
            e.preventDefault();
            counter++

            /*  --------------- clean form  --------------- */
            const emptyCard = document.getElementById('empty-card')
               .cloneNode(true)
               .cloneNode(true);


            emptyCard.classList.remove('d-none');
            emptyCard.removeAttribute('id')
            console.log(emptyCard)

            /* data clean form, set name attributes */
            const theme = emptyCard.querySelector('.form__theme');
            console.log(theme);
            jodit(theme);
            formParamName(theme);

            const authors = emptyCard.querySelector('.form__authors');
            console.log(authors);
            formParamName(authors);

            let fileUrl = emptyCard.querySelectorAll('.form__file_url');
            fileUrl = fileUrl[fileUrl.length - 1];
            console.log(fileUrl);
            formParamName(fileUrl);

            /* set id in form */
            function formParamName (param) {
               return param.setAttribute("name", param.getAttribute("name") + counter);
            }
            /*  --------------- clean form end --------------- */

            /*  btn MINUS */
            // const div = document.createElement("div");
            // const button = document.createElement("button");
            // button.classList.add("btnMinus");
            // button.textContent = '-';
            // div.append(button);
            // emptyCard.append(div);

            /* --------------- create new form ------------------ */
            row.append(emptyCard);

            /* --------------- jodit ------------------ */



            /* jodit clean */
            // let jodits = document.querySelectorAll('.jodit_wysiwyg.format')
            // let joditLastElem = jodits[jodits.length - 1];
            // let joditTextArea = joditLastElem.children;
            // joditTextArea[0].innerText = "";

            /* pdf icon remove */
            // let pdfIco = document.querySelectorAll('.files__ico');
            // pdfIco[pdfIco.length - 1].remove();
            btnMinus()
         })
         btnMinus()
      })

      function btnMinus() {
         /*  --------------- btn minus  --------------- */
         const minus = document.querySelectorAll('.btnMinus');

         minus.forEach(item => {
            item.addEventListener('click', (e) => {
               e.preventDefault();

               const closest = e.target.closest('.form__group')

               closest.remove();
            })
         })
      }

      function jodit(data) {
         new Jodit(data,
            {
               language: 'ru',
               buttons: [
                  'paragraph', 'align',            '|',
                  'bold', 'underline', 'italic',   '|',
                  'ul', 'ol',                      '|',
                  'link', 'image', 'video',        '|',
                  'table',                         '|',
                  'undo', 'redo',                  '|',
                  'fullsize', 'print',             '|',
                  'source',
               ],
               editorCssClass: 'format',
               addNewLineOnDBLClick: false,
               link: {
                  followOnDblClick: false,
               },
               useNativeTooltip: true,
               events: {
                  afterInit: function (e) {
                     var t = this;
                  },
               },
               uploader: {
                  url: $(this).data('uploader'),
                  format: 'json',
                  headers: {
                     'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                  },
                  isSuccess: function (e) {
                     console.log(e);
                     return e.success;
                  },
                  error: function(e) {
                     console.log('error', e);
                  },
                  contentType: function(e){
                     return (void 0 === this.jodit.ownerWindow.FormData || 'string' == typeof e) && 'application/x-www-form-urlencoded; charset=UTF-8';
                  }
               },
               filebrowser: {
                  buttons: [
                     "filebrowser.update",
                     "filebrowser.remove",
                     "filebrowser.select",
                     "|",
                     "filebrowser.tiles",
                     "filebrowser.list",
                     "|",
                     "filebrowser.sort"
                  ],
                  deleteFolder: false,
                  ajax: {
                     url: $(this).data('filebrowser'),
                     method: 'POST',
                     headers: {
                        'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                     },
                  },
                  isSuccess: function (e) {
                     console.log(e);
                     // console.log(e.debug);
                     return e.success;
                  },
                  error: function(e) {
                     console.log('error', e);
                     // alert('Ошибка. Подробности в консоли');
                  },
               }
            });
      }

   </script>
@endsection

<style type="text/css">
   .form__group {
      padding-top: 15px;
   }

   .btnMinus{
      background: black;
      border: 1px solid black;
      border-radius: 2em;
      color: white;
      font-size: 12px;
      height: 17px;
      line-height: 2px;
      margin: 0 0 20px 0;
      width: 17px;
   }
</style>
