<?php

namespace App\Http\Controllers\Admin;

use App\Jobs\VideoMainMp4ToHls;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\Support\Renderable;
use App\Classes\RemoveFiles;
use App\Facades\Uploader;

use App\Gallery;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;

class GalleryController extends AdminController
{
   protected $model;

   public function __construct ()
   {
      parent::__construct();
   }

   public function init ()
   {
      $this->setPage([
         'route' => 'admin.gallery',
         'title' => 'Gallery - [ ADMIN ]',
         'h1'    => 'Gallery'
      ]);

      $this->setModel(Gallery::class);
      $this->setForm();
      $this->setRules();
   }

   public function index (Request $request)
   {
      $this->init();

      $onlyType = $request->get('only');

      $items = $this->model::orderBy('id', 'desc');
      if ($onlyType === 'video' || $onlyType === 'image') {
         $items = $items->where('type', $onlyType);
      }

      if ($request->ajax()) {
         return response()->json($items
            ->where([
               ['converted', '!=', 3],
               ['converted', '!=', 4]
            ])
            ->orWhereNull('converted')
            ->get()
         );
      }

      $items = $items->paginate(10);

      $data = [
         'page'  => $this->getPage(),
         'items' => $items,
         'ext_scripts' => [
            '<script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>',
            '<script src="'.config('app.url').'/js/inc/video-hls.js"></script>',
         ]
      ];

      return view('admin._list', $data);
   }

   public function create (): Renderable
   {
      $this->init();
      $this->setCurrent('create');

      $data = [
         'page' => $this->getPage(),
         'form' => $this->getForm(),
      ];
      return view('admin._form', $data);
   }

   private function preUpdating(Request $request, string $type) {
      switch($type) {
         case 'image';
            $request->merge([
               'type' => 'image',
               'mime_type' => 'mimetypes:image/*'
            ]);
            break;
         case 'video';
            $request->merge([
               'type' => 'video',
               'mime_type' => 'mimetypes:video/mp4,application/x-mpegURL'
            ]);
            break;
      }
   }

   public function store (Request $request): RedirectResponse
   {
      $this->init();
      $request->validate($this->rules);
      $this->preUpdating($request, $request->get('type'));
      $item = Uploader::create($this->model);

      return redirect()
         ->route("{$this->page['route']}.index")
         ->with('alert', "Added: {$item->type} <b>{$item->name}</b>");
   }

   public function edit (int $id): Renderable
   {
      $this->init();
      $this->setCurrent('edit');

      $item = $this->model::findOrFail($id);
      $this->setForm('url.items', $this->getFiles(
         $item,
         'url',
         $item->type === 'video' ? ($item->converted === 2 ? [0] : ($item->available_formats ? [$item->available_formats[0]] : [])) : ['min', ''],
         'application/x-mpegURL'
      ));
      $this->setForm('url.disabled', ($item->converted === 3));
      $this->setForm('poster_url.items', $this->getFiles($item, 'poster_url'));


      $data = [
         'page' => $this->getPage(),
         'form' => $this->getForm(),
         'item' => $item,
         'ext_scripts' => [
            '<script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>',
            '<script src="'.config('app.url').'/js/inc/video-hls.js"></script>',
            '<script>
               window.onload = () => {
                  setHlsVideos(\'video-hls\');
               }
            </script>'
         ]
      ];

      return view('admin._form', $data);
   }

   public function update ($id, Request $request): RedirectResponse
   {
      $this->init();
      $item = $this->model::findOrFail($id);
      $this->preUpdating($request, $item->type);
      $this->setRule('url', $request->mime_type);
      $request->validate($this->rules);
      Uploader::update($item);

      return redirect()
         ->route("{$this->page['route']}.index")
         ->with('alert', "Updated: {$item->type} <b>{$item->name}</b>");
   }

   public function destroy (int $id, Request $request)
   {
      $this->init();
      $removeVideo = new RemoveFiles(Gallery::findOrFail($id));
      $removeVideo->RemoveDirectory();

      return $this->delete($id, $request);
   }

   protected function checkDrop ($item): bool {
      return true;
   }

   public function reconvertMp4ToHls (int $id): JsonResponse
   {
      $item = Gallery::findOrFail($id);

      if ($item->type !== 'video') {
         return response()->json("<b>{$item->id}</b> is not a video");
      }
      /* change status to 3 - means convertation in process */
      $data = ['converted' => 3];
      $item->update($data);

      Queue::connection("longFilesJob")->push((new VideoMainMp4ToHls(
         $item,
         'available_formats',
         'id',
         'gallery',
         'gallery',
         'url'
      )));

      return response()->json("Reconvertation started: <b>{$item->id}</b>");
   }
}
