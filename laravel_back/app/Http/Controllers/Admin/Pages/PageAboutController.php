<?php

namespace App\Http\Controllers\Admin\Pages;

use App\Http\Controllers\Admin\AdminController;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\Support\Renderable;

use App\Page;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class PageAboutController extends AdminController
{
	protected $model;

	public function __construct ()
	{
		parent::__construct();
	}

	public function init () {
		$this->setPage([
			'route' => 'admin.page-about-edit',
			'title' => 'About - [ ADMIN ]',
			'h1'    => 'About',
		]);
		$this->setModel(Page::class);
		$this->setForm();
		$this->setRules();
	}

   /**
    * @param Request $request
    * @return RedirectResponse
    */
   public function index(Request $request): RedirectResponse
   {
      $this->init();

      return redirect()->route('admin.page-about-edit.edit', ['id' => 5]);
   }

   /**
    * @param int $id
    * @return Renderable
    */
   public function edit (int $id): Renderable
   {
      $this->init();
      $this->setCurrent('edit');

      $item = $this->model::findOrFail($id);

      $id = $item->id;

      $item->content = json_decode($item->content, true);

      $data = [
         'page' => $this->getPage(),
         'form' => $this->getForm(),
         'id' => $id,
         'gallery' => $item->gallery()->orderBy('order')->get()
      ];

      if(isset($item->content['publications'])) {
         $item = $item->content['publications'];
         $data['item'] = $item;
      }

      return view('admin.pages.page-about', $data);
   }

   public function updateGallery($data, $item) {
      if (isset($data['gallery'])) {
         $item->gallery()->detach();
         $galleryArr = json_decode($data['gallery']);
         foreach ($galleryArr as $index => $id) {
            $item->gallery()->sync([5 => [
               'gallery_id' => $id,
               'order' => $index,
               'created_at' => now()
            ]], false);
         }
      }
   }

   /**
    * @param int $id
    * @param Request $request
    * @return RedirectResponse
    */
   public function update (int $id, Request $request): RedirectResponse
   {
      $this->init();

      $data = $request->all();

      /* get id forms */
      $pattern = '/[^_]+$/';

      /* last key */
      $last_key = '';
      foreach($data as $key => $value) {
         if(!($value instanceof UploadedFile)) {
            $last_key = $key;
         }
      }

      /* ids form */
      $ids = [];
      foreach($data as $key => $value) {
         if($key === '_token' || $key === '_method') continue;
         preg_match($pattern, $key, $output_array);
         array_push($ids, $output_array[0]);
      }
      $ids = array_unique($ids);

      /* last key value */
//      preg_match($pattern, $last_key, $output_array);
//      $count_forms = $output_array[0] ?? 0;


      /* make new array */
      $forms = [];
      foreach($ids as $key => $value){
         if(isset($data['theme_'.$value]) || isset($data['authors_'.$value])) {
            if(isset($data['file_url_'.$value])) {
               array_push($forms, [
                  'id' => (int)$value,
                  'theme' => $data['theme_'.$value] ?? null,
                  'authors' => $data['authors_'.$value] ?? null,
                  'file_url' => $data['file_url_'.$value] ?? null,
                  'file_ext' => 'pdf'
               ]);
            } else {
               array_push($forms, [
                  'id' => (int)$value,
                  'theme' => $data['theme_'.$value] ?? null,
                  'authors' => $data['authors_'.$value] ?? null,
               ]);
            }
         }
      }

      /* validation */
      $rules = [
         'id' => 'nullable',
         'theme' => 'nullable',
         'authors' => 'nullable',
         'file_url' => 'nullable|mimes:pdf',
         'file_ext' => 'nullable|string'
      ];
      foreach($forms as $value) {
         $validator = Validator::make($value, $rules, [
            'form' => [
               'theme' => [
                  'type'      => 'wysiwyg',
                  'signature' => 'Theme',
                  'wysiwyg'   => true,
                  'media'     => 'media/wisiwyg/pages/about/'
               ],
               'authors' => [
                  'type'      => 'text',
                  'signature' => 'Authors',
               ],
               'file_url' => [
                  'type'      => 'text',
                  'signature' => 'File url',
               ],
               'file_ext' => [
                  'type'      => 'text',
                  'signature' => 'File ext',
               ],
            ],
         ]);
         if ($validator->fails()) {
            /* redirect about page */
            return redirect()->route('admin.page-about-edit.edit', ['id' => 5])
               ->withErrors($validator)
               ->withInput();
         }
      }


      /* upload file */

      foreach($forms as $form_key => $form_value) {
         foreach($form_value as $param_key => $param_value) {
            if($param_key === 'file_url' && $param_value->getMimeType() === 'application/pdf') {
               /* return path to file */
               $forms[$form_key][$param_key] = $this->fileUpload($param_value, $form_value['id'] ?? $form_key);
            }
         }
      }

      /* ---------------- NEW ARRAY ---------------- */

      /* get content */
      $item = $this->model::findOrFail($id);
      $this->updateGallery($request->all(), $item);
      $item->content = json_decode($item->content, true);
      $data = $item->content;

      /* id in new array */
      $idsNewForms = [];
      foreach($forms as $index => $value) {
         array_push($idsNewForms, $value['id']);
      }

      /* replace new and old data */
      $idsPublication = [];
      foreach($data['publications'] as $index => $value) {
         array_push($idsPublication, $value['id'] ?? $index + 1);
      }

      /* count forms */
      $newArrPublication = [];
      $formsCount = count($forms);
      $dataPublicationsCount = count($data['publications']);

      if($formsCount <= $dataPublicationsCount) {
         for($i = 0; $i < $formsCount; $i++) {
            $publicationNum = array_search($idsNewForms[$i], $idsPublication);
            if($publicationNum) {
               if(gettype($publicationNum) === 'integer') {
                  array_push($newArrPublication, array_merge($data['publications'][$publicationNum], $forms[$i]));
               }
            } else {
               array_push($newArrPublication, array_merge($forms[$i]));
            }
         }
      } else {

         for($i = 0; $i < $formsCount; $i++) {
            if($i < $dataPublicationsCount) {
               $publicationNum = array_search($idsNewForms[$i], $idsPublication);
               if(gettype($publicationNum) === 'integer') {
                  array_push($newArrPublication, array_merge($data['publications'][$publicationNum], $forms[$i]));
               }
            } else {
               array_push($newArrPublication, array_merge($forms[$i]));
            }
         }
      }

      /* ---------------- NEW ARRAY END ---------------- */

      /* save content */
      $data['publications'] = $newArrPublication;
      $item->content = json_encode((object) $data, JSON_UNESCAPED_UNICODE);
      $item->save();
      return back()->with('alert', "Edited");
   }

   /**
    * @param object $file
    * @param int $form_key
    * @return string
    */
   public function fileUpload(object $file, int $form_key): string
   {
      /* uploader save file */
      $mimeType = $file->getMimeType();
      if($mimeType === 'application/pdf') {
         if(File::isDirectory(public_path("media/pages/about/publications/{$form_key}/"))) {
            File::deleteDirectory(public_path("media/pages/about/publications/{$form_key}/"));
         }
         $file->move(public_path("media/pages/about/publications/{$form_key}/"), $file->getClientOriginalName());
      }
      return "media/pages/about/publications/{$form_key}/".$file->getClientOriginalName();
   }
}
