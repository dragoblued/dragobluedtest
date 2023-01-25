<?php

namespace App\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Intervention\Image\ImageManagerStatic as Image;
use function Tinify\fromFile;
use function Tinify\setKey;
//use FFMpeg as FFMpeg;

class PosterFromFrame implements ShouldQueue
{
   use Dispatchable;
   use InteractsWithQueue;
   use Queueable;
   use SerializesModels;

   private $item;
   private $posterField;
   private $folder;
   private $subFolder;
   private $name;
   private $nameSuffix;
   private $srcFilePath;
   private $frameSec;

   /**
    * Poster From Frame.
    *
    * @param $item
    * @param string $folder
    * @param string $subfolder
    * @param string $name
    * @param string $nameSuffix
    * @param string $srcFilePath
    * @param int $frameSec
    */
   public function __construct($item, string $posterField, string $folder, string $subFolder, string $name, string $nameSuffix,
                               string $srcFilePath, $frameSec = 15)
   {
      $this->item = $item;
      $this->posterField = $posterField;
      $this->folder = $folder;
      $this->srcFilePath = $srcFilePath;
      $this->subFolder = $subFolder;
      $this->name = $name;
      $this->nameSuffix = $nameSuffix;
      $this->srcFilePath = $srcFilePath;
      $this->frameSec = ((int) $frameSec >= 0) ? (int) $frameSec : 15;
   }

   public function handle()
   {
      Log::debug('posterFromFrame '.$this->srcFilePath);
//      $srcPath = public_path("media/{$this->srcFilePath}");
      if (!File::exists($this->srcFilePath)) {
         throw new Exception('Source File does not exist.');
      }
      $dest = public_path("media/{$this->folder}/{$this->subFolder}/{$this->name}{$this->nameSuffix}.jpg");
//      FFMpeg::fromDisk($this->srcFolder)
//         ->open($this->videoPath)
//         ->getFrameFromSeconds($this->frameSec)
//         ->export()
//         ->toDisk($this->folder)
//         ->save("{$this->subfolder}/{$this->namePrefix}_poster.jpg");
      $mainCmd = config('laravel-ffmpeg.ffmpeg.binaries')." -ss {$this->frameSec} -y -i {$this->srcFilePath} -vframes:v 1 -q:v 2 {$dest} 2>&1";
      Log::debug($mainCmd);
      shell_exec($mainCmd);

      $this->handlePoster($dest);
      $this->item[$this->posterField] = "media/{$this->folder}/{$this->subFolder}/{$this->name}{$this->nameSuffix}.jpg";

      $this->item->save();
   }

   private function handlePoster($imagePath)
   {
      setKey("RfmssKKdFbbTwkVHMkz3jqjpmj9kG6QM");
      $source = fromFile($imagePath);
      $source->toFile($imagePath);
      $sizers =  [
         '_min' => [ 0.5, 90 ],
         '_preload' => [ 0.1, 50 ]
      ];
      foreach ($sizers as $size => $set) {
         $image = Image::make($imagePath);
         $width = $image->height();
         $height = $image->width();
         if (!is_null($set[0])) {
            $image->widen(round($set[0] * $width));
         } else {
            $image->heighten(round($set[0] * $height));
         }
         $upload = str_replace('.', $size.'.', $imagePath);
         $image->save($upload, $set[1]);
      }
   }
}
