<?php

namespace App\Services;

use App\Jobs\RemoveVideo;
use App\Jobs\VideoGallery;
use App\Jobs\PosterFromFrame;
use App\Jobs\VideoHLS;
use App\Jobs\VideoMain;
use App\Jobs\VideoMainHLS;
use App\Jobs\VideoPromo;
use App\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use Intervention\Image\ImageManagerStatic as Image;
use Owenoj\LaravelGetId3\GetId3;
use stdClass;
use function React\Promise\map;
use function Tinify\fromFile;
use function Tinify\setKey;

class Uploader
{
   private $request;

   public function __construct(Request $request)
   {
      $this->request = $request;
   }

   /**
    * @param $model
    * @return mixed
    * @throws \getid3_exception
    */
   public function create ($model)
   {
      $request = $this->request;
      $replace = [];
      $folders = $model::FILES;
      foreach ($folders as $field => $file) {
         if ($request->has($field)) {
            $replace[$field] = 'ID_fake';
         }
      }

      $requestAll = $request->all();
      $requestAll = array_merge($requestAll, $replace);
      $created = $model::create($requestAll);

      return $this->update($created);
   }

   /**
    * @param $item
    * @return mixed
    * @throws \getid3_exception
    */
   public function update ($item)
   {
      $request = $this->request;
      $replace = [];
      $settings = $this->settings($item); // poster_url []
      foreach($settings as $setting) {
         $setting->folder = "{$setting->folder}{$item->table}/";
      }
      $videoSettings = $this->settings($item, 'VIDEOS');
      foreach($videoSettings as $setting) {
         $setting->folder = "{$setting->folder}{$item->table}/";
      }

      // upload
      foreach ($settings as $field => $set) {
         if ($request->has($field.'_delete')) {
            $replace[$field] = $this->remove($item, $field);
         } elseif ($request->has($field) && $set->type === 'files') {
            $replace[$field] = $this->upload($item, $field, $set);
         }
      }

      foreach ($videoSettings as $field => $set) {
         if ($request->has($field . '_delete')) {
//            $formats = isset($set->formatsField) ? array_map(function ($item) {
//               return '_'.$item;
//            }, $item[$set->formatsField] ?? []) : [''];
//            $this->remove($item, $field, $formats);
            Queue::connection("longFilesJob")->push((new RemoveVideo($item, $field)));
            $replace[$field] = null;
            $replace['converted'] = 0;
            if (isset($set->formatsField)) {
               $replace[$set->formatsField] = null;
            }
         }
      }

      $data = $request->all();

      $data = array_merge($data, $replace);

      $item->fill($data);
      // video url, save file
      if (isset($data['type']) && $data['type'] === 'video') {
         if (isset($data['url'])) {
//            $track = new GetId3($item->url);
//            $url_extract = $track->extractInfo();
            $url = Str::random(16).'.'.$item->url->getClientOriginalExtension();
            $item->url->move(public_path()."/media/gallery/{$item->id}/", $url);
            $item->url = "media/{$item->table}/{$item->id}/{$url}";
            $item->converted = 3;
         }
      }
      if (isset($data['promo_video_url'])) {
         $track = new GetId3($item->promo_video_url);
         $promo_video_extract = $track->extractInfo();
         if (isset($promo_video_extract['playtime_seconds'])) {
            $item->promo_video_duration = floor($promo_video_extract['playtime_seconds']);
         }
         $promo_video_url = Str::random(16).'.'.$item->promo_video_url->getClientOriginalExtension();
         $item->promo_video_url->move(public_path()."/media/{$item->table}/{$item->name}/", $promo_video_url);
         $item->promo_video_original_name = $item->promo_video_url->getClientOriginalName();
         $item->promo_video_url = "media/{$item->table}/{$item->name}/{$promo_video_url}";
         $item->promo_video_available_formats = null;
         $item->converted = false;
      }
      // video_url, save file
      if (isset($data['video_url'])) {
//         $track = new GetId3($item->video_url); // video seconds get GetId3 package
//         $video_extract = $track->extractInfo();
//         if (isset($video_extract['playtime_seconds'])) {
//            $item->video_duration = floor($video_extract['playtime_seconds']); // video seconds get GetId3 package
//         }
         $video_url = Str::random(16).'.'.$item->video_url->getClientOriginalExtension();
         $item->video_url->move(public_path()."/media/{$item->table}/{$item->name}/", $video_url);
         $item->video_original_name = $item->video_url->getClientOriginalName();
         $item->video_url = "media/{$item->table}/{$item->name}/{$video_url}";
         /* change status to 3 - means convertation in process */
         $item->converted = 3;
      }
      if (isset($data['video_url_delete'])) {
         $this->removeLessonsWithPromo($item);
         $item->video_url = null;
         $item->promo_video_url = null;
         $item->converted = 0;
         $item->video_available_formats = null;
         $item->promo_video_available_formats = null;
      }

      $item->save();

      // video start convert GALLERY
      if (isset($data['type']) && $data['type'] === 'video') {
         if (isset($data['url'])) {
            Queue::connection("longFilesJob")->push((new RemoveVideo($item, 'url')));
            Log::debug($item->url);
            Queue::connection("longFilesJob")->push((new VideoHLS(
               $item,
               'id',
               'available_formats',
               public_path($item->url),
               'gallery',
               'url',
               $request->has('should_replace_poster'),
               $request->get('video_frame_second'),
               'poster_url'
            )));
         } elseif ($request->has('should_replace_poster')) {
            if ($item->id && is_array($item->available_formats)) {
               $idx = count($item->available_formats) - 1;
               $ext = $item->converted === 2 ? 'm3u8' : ($item->converted === 1 ? 'mp4' : null);
               if (is_null($ext)) {
                  throw new Exception('Extension of file is not defined by converting status.');
               }
               Queue::connection("longFilesJob")->push((new PosterFromFrame(
                  $item,
                  'poster_url',
                  'gallery',
                  $item->id,
                  $item->id,
                  '_poster',
                  public_path("media/gallery/{$item->id}/{$item->id}_{$idx}.{$ext}"),
                  $request->get('video_frame_second')
               )));
            }
         }
      }
      if (isset($data['promo_video_url'])) {
         Queue::connection("longFilesJob")->push((new RemoveVideo($item, 'promo_video_url')));
         Queue::connection("longFilesJob")->push((new VideoPromo($item, $promo_video_extract['video'], $promo_video_extract['bitrate'])));
      }
      if (isset($data['video_url'])) {
//         Queue::connection("longFilesJob")->push((new RemoveVideo($item, 'video_url')));
//         Queue::connection("longFilesJob")->push((new VideoMain(
//            $item, $video_extract, $request->has('should_replace_poster'), $request->get('video_frame_second'), $request->has('should_update_total_duration')
//         )));
         Queue::connection("longFilesJob")->push((new RemoveVideo($item, 'video_url')));
         Queue::connection("longFilesJob")->push((new VideoMainHLS(
            $item, 'video_url', 'hls', 'poster_url', $request->has('should_replace_poster'), $request->get('video_frame_second'), $request->has('should_update_total_duration')
         )));
      } elseif ($request->has('should_replace_poster')) {
         if ($item->route && is_array($item->video_available_formats)) {
            $count = count($item->video_available_formats);
            $formatIdx = $count - 1;
            $videoPath = $item->video_type === 'm3u8'
               ? "{$item->name}/{$item->name}_{$formatIdx}.m3u8"
               : "{$item->name}/{$item->name}_{$item->video_available_formats[$count - 1]}.mp4";
            $srcFolder = $item->video_type === 'm3u8' ? 'hls' : 'lessons';
            Queue::connection("longFilesJob")->push((new PosterFromFrame(
               $item,
               'poster_url',
               'lessons',
               $item->name,
               $item->name,
               '_poster',
               public_path("media/{$srcFolder}/{$videoPath}"),
               $request->get('video_frame_second')
            )));
         }
      }

      return $item;
   }

   /**
    * @param $item
    * @return false|\Illuminate\Http\JsonResponse
    */
   public function files ($item)
   {
      $request = $this->request;
      if($request->filled('delete')) {
         $this->slice($item, $request->field, $request->delete);
         return response()->json('Deleted');
      }
      if($request->filled('shuffle')) {
         $this->shuffle($item, $request->field, $request->shuffle);
         return response()->json('Shuffled');
      }

      return false;
   }

   protected function remove ($item, string $field, array $formats = ['', '_min', '_preload'])
   {
      if (!$item[$field]) {
         return null;
      }
      foreach ($formats as $suffix) {
         $this->removeFile(public_path(str_replace('.', $suffix.'.', $item[$field])));
      }
      return null;
   }

   protected function removeLessonsWithPromo ($item)
   {
      if (!$item['name'] || !$item['promo_video_url']) {
         return null;
      }
      $this->removeDir(public_path("media/hls/{$item['name']}"));
      foreach ($item['promo_video_available_formats'] as $suffix) {
         $this->removeFile(public_path(str_replace('.', '_'.$suffix.'.', $item['promo_video_url'])));
      }
      return null;
   }

   /**
    * @param $item
    * @param string $field
    * @param stdClass $settings
    * @return array|mixed|string
    */
   protected function upload ($item, string $field, stdClass $settings)
   {
      $request = $this->request;

      $names = $item[$settings->names];
      /**
       * full..public/media/lessons/basic-lesson/
       * //        $folder = public_path(); // public/
       */
      $folder = $this->checkDir($settings->dir.$settings->folder).$names.'/';

      $files = [];
      $fnames = [];

      // TODO multiple upload
      if($settings->multiple) {
//            $files = $request[$field];
//            $folder = $this->checkDir("{$settings->folder}{$item->id}/");
      } else {
         $files[] = $request[$field];
      }

      foreach ($files as $file) {
         $ext = $file->getClientOriginalExtension();
         $fileName = explode('.', $file->getClientOriginalName())[0];
         // Prefix
         $naming = $settings->multiple ? null : $settings->prefix ?? '';

         /**
          * Basic name file
          * // $fname = "{$settings->folder}{$names}{$naming}.{$ext}";
          */
         $fname = "{$fileName}{$naming}.{$ext}";

         // Move to dir
         $file = $file->move($folder, $fname);

         Log::debug($ext);

         $imageAllowedExt = ['jpeg','png', 'jpg'];
         if (in_array($ext, $imageAllowedExt)) {
            // Use Tinify package
            setKey("RfmssKKdFbbTwkVHMkz3jqjpmj9kG6QM");
            $source = fromFile($file);
            $source->toFile($file);
         }

         $this->sizers($settings, $file, $settings->folder, $item, $fileName);

         // New name for save db
//            $fname = $settings->folder.$names.'/'.$fname;
         $fnames[] = $fname;
      }

      // TODO multiple
      if(!$settings->multiple) {
         $fnames = $settings->folder.$names.'/'.$fnames[0];
      } elseif(is_array($item[$field])) {
         $fnames = array_merge($item[$field], $fnames);
      }

      return $fnames;
   }

   private function sizers (stdClass $settings, $file, string $folder, $item, $fileName)
   {
      $names = $item[$settings->names];
      if(property_exists($settings, 'sizers') == false) {
         return;
      }
      foreach ($settings->sizers as $key => $value) {
         if ($key === 'static') {

            foreach ($value as $size => $set) {
//                    $sizeFolder = $this->checkDir("{$folder}{$request->route}/{$size}/");
//
//                    $image = Image::make($file);
//
//                    if (!is_null($set[0])) {
//                        $image->widen(round($set[0]));
//                    } else {
//                        $image->heighten(round($set[1]));
//                    }
//
//                    $upload = "{$sizeFolder}{$fname}";
//
//                    $image->save($upload, $set[2]);
            }
         } elseif ($key === 'dynamic') {
            foreach ($value as $size => $set) {
               $sizeFolder = $this->checkDir($settings->dir.$settings->folder).$names.'/';
               $image = Image::make($file);
               $width = $image->height();
               $height = $image->width();

               if (!is_null($set[0])) {
                  $image->widen(round($set[0] * $width));
               } else {
                  $image->heighten(round($set[0] * $height));
               }

               $upload = "{$sizeFolder}{$fileName}{$settings->prefix}{$size}.{$image->extension}";
               $image->save($upload, $set[1]);
            }
         }
      }
   }

   public function drop ($item, string $field = null): void
   {
      $src = [];
      $settings = $this->settings($item);
      foreach ($settings as $key => $set) {
         // UPDATE (only changed items)
         if(!is_null($field) && $key != $field) {
            continue;
         }

         $src[] = public_path($item["{$key}_src"]);
         if(!property_exists($set, 'sizers')) {
            continue;
         }

         $sizers = array_keys($set->sizers);
         foreach ($sizers as $size) {
            $src[] = public_path($item["{$key}_{$size}"]);
         }
      }
      File::delete($src);
   }

   private function slice ($item, string $field, int $pos): void
   {
      $files = call_user_func([ $item, $field ]);
      File::delete(public_path($files[$pos]));

      $data = $item[$field];
      array_splice($data, $pos, 1);
      $item[$field] = $data;
      $item->save();
   }

   private function shuffle ($item, string $field, array $order): void
   {
      $files = $item[$field];
      $data = [];
      foreach ($order as $i) {
         $data[] = $files[$i];
      }
      $item[$field] = $data;
      $item->save();
   }

   private function settings ($item, $type = 'FILES'): array
   {
      $fields = $type === 'VIDEOS' ? $item::VIDEOS : $item::FILES;
      $itemTable = str_replace('_','.',$item->table);
      $form = config("admin.{$itemTable}.form");

      $settings = [];

      foreach ($fields as $field => $folder) {
         // Check Gallery mutating type field 'url'
         if ($field === 'url' && isset($item->type)) {
            if ($item->type === 'video') {
               if ($type !== 'VIDEOS') {
                  continue;
               }
            } elseif ($item->type === 'image') {
               if ($type !== 'FILES') {
                  continue;
               }
            }
         }
         if (isset($form[$field])) {
            $set = $form[$field];
//         unset($set['type'], $set['signature']);
            $settings[$field] = (object) array_merge(
               [ 'folder' => $folder ],
               $set
            );
         }
      }

      return $settings;
   }

   private function checkDir ($dir): string
   {
      $dir = base_path($dir);
      if(!File::exists($dir)) {
         File::makeDirectory($dir);
      }
      return $dir;
   }

   private function removeFile ($path): string
   {
      if(File::exists($path)) {
         File::delete($path);
         return true;
      }
      return false;
   }

   private function removeDir ($path): string
   {
      if(File::isDirectory($path)) {
         File::deleteDirectory($path);
         return true;
      }
      return false;
   }

   /**
    * version evrodentist
    */
//	private $request;
//
//	public function __construct(Request $request)
//	{
//		$this->request = $request;
//	}
//
//	public function create ($model)
//	{
//		$request = $this->request;
//		$replace = [];
//		$folders = $model::FILES;
//
//		foreach ($folders as $key => $file) {
//			if($request->has($key)) {
//				$replace[$key] = 'ID_fake';
//			}
//		}
//
//		$data = $request->all();
//		$data = array_merge($data, $replace);
//
//		$created = $model::create($data);
//		return $this->update($created, true);
//	}
//
//	public function update (object $item, bool $create = false)
//	{
//		$request = $this->request;
//		$replace = [];
//		$settings = $this->settings($item);
//
//		foreach ($settings as $field => $set) {
//			if(!$request->has($field)) {
//				continue;
//			}
//
//			if(!$create) {
//				$this->drop($item, $field);
//			}
//			$replace[$field] = $this->upload($item, $field, $set);
//		}
//		$data = $request->all();
//		$data = array_merge($data, $replace);
//
//		$item->fill($data);
//		$item->save();
//
//		return $item;
//	}
//
//	public function upload (object $item, string $field, object $settings)
//	{
//		$request = $this->request;
//		$file = $request[$field];
//		$folder = $this->checkDir($settings->folder);
//		$ext = $file->getClientOriginalExtension();
//		$fname = "{$item->id}.{$ext}";
//		if(property_exists($settings, 'sizers')) {
//			foreach ($settings->sizers as $size => $set) {
//				$sizeFolder = $this->checkDir("{$settings->folder}{$size}/");
//				$upload = "{$sizeFolder}{$fname}";
//				$image = Image::make($file);
//				if(!is_null($set[0])) {
//					$image->widen($set[0]);
//				}
//				else {
//					$image->heighten($set[1]);
//				}
//				$image->save($upload, $set[2]);
//			}
//		}
//		$file->move($folder, $fname);
//		return $ext;
//	}
//
//	public function drop (object $item, string $field = null): void
//	{
//		$src = [];
//		$settings = $this->settings($item);
//		foreach ($settings as $key => $set) {
//			// UPDATE (only changed items)
//			if(!is_null($field) && $key != $field) {
//				continue;
//			}
//
//			$src[] = public_path($item["{$key}_src"]);
//			if(!property_exists($set, 'sizers')) {
//				continue;
//			}
//
//			$sizers = array_keys($set->sizers);
//			foreach ($sizers as $size) {
//				$src[] = public_path($item["{$key}_{$size}"]);
//			}
//		}
//		File::delete($src);
//	}
//
//	private function settings ($item): array
//	{
//		$settings = $item::FILES;
//		$form = config("admin.{$item->table}.form");
//		foreach ($settings as $field => $folder) {
//			$set = $form[$field];
//			unset($set['type'], $set['signature']);
//			$settings[$field] = (object) array_merge(
//				[ 'folder' => $folder ],
//				$set
//			);
//		}
//		return $settings;
//	}
//
//	private function checkDir ($dir): string
//	{
//		$dir = public_path($dir);
//		if(!File::exists($dir)) {
//			File::makeDirectory($dir);
//		}
//		return $dir;
//	}
}
