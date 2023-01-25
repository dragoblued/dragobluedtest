<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;
use FFMpeg;
use FFMpeg\Coordinate\Dimension;
use FFMpeg\Format\Video\WebM;
use FFMpeg\Format\Video\X264;
use Illuminate\Support\Facades\Log;

class VideoPromo implements ShouldQueue
{
   use Dispatchable;
   use InteractsWithQueue;
   use Queueable;
   use SerializesModels;

   private $item;
   private $params;
   private $bitrate;
   private $names;

   /**
    * Video Promo.
    *
    * @param $item
    * @param $params
    * @param $bitrate
    */
   public function __construct($item, $params, $bitrate)
   {
      $this->item = $item;
      $this->params = $params;
      $this->bitrate = $bitrate;
      $this->names = $this->settings($item);
   }

   /**
    * Find video folder
    *
    * @param $item
    * @return string
    */
   private function settings ($item): string
   {
      // Settings
      $settings = $item::VIDEOS;
      $itemTable = str_replace('_','.',$item->table);
      $form = config("admin.{$itemTable}.form");

      foreach ($settings as $field => $folder) {
         $set = $form[$field];
         unset($set['type'], $set['signature']);
         $settings[$field] = (object) array_merge(
            [ 'folder' => $folder ],
            $set
         );
      }

      foreach ($settings as $value) { // $settings foreach key => value
         if ($value->names) {
            $names = $this->item[$value->names];
         }
      }

      return $names;
   }

   public function handle(): void
   {
      /* ---------------------------------- Video settings ----------------------------- */

      /**
       * Video settings
       *
       * Settings video formatter
       *
       * $codec - set audioCodec and videoCodec
       * $resolution - set video quantity of format .mp4 | webm
       * $kiloBitrate - set rate
       * $dimension - set size of window player
       */
      $codec = collect([
         'libx264' => (new X264('aac', 'libx264'))->setAudioKiloBitrate(256),
         'libvpx' => (new WebM('libvorbis', 'libvpx'))->setAudioKiloBitrate(256),
      ]);

      $resolution = collect([
         240,
         360,
         480,
         720,
         1080,
      ]);

      $resolutionY = $this->params['resolution_y']; // Check quantity formats

      $kb_rate = $this->bitrate/1000;

      $kiloBitrate = collect([]);

      switch ($resolutionY) {
         case $resolutionY < 240:
            $quantityFormats = 0;
            break;
         case $resolutionY >= 240 && $resolutionY < 360:
            $quantityFormats = 1;
            $kiloBitrate->push($kb_rate);
            break;
         case $resolutionY >= 360 && $resolutionY < 480:
            $quantityFormats = 2;
            $kiloBitrate->push($kb_rate * 0.17);
            $kiloBitrate->push($kb_rate);
            break;
         case $resolutionY >= 480 && $resolutionY < 720:
            $quantityFormats = 3;
            $kiloBitrate->push($kb_rate * 0.13);
            $kiloBitrate->push($kb_rate * 0.75);
            $kiloBitrate->push($kb_rate);
            break;
         case $resolutionY >= 720 && $resolutionY < 1080:
            $quantityFormats = 4;
            $kiloBitrate->push($kb_rate * 0.03);
            $kiloBitrate->push($kb_rate * 0.20);
            $kiloBitrate->push($kb_rate * 0.26);
            $kiloBitrate->push($kb_rate);
            break;
         case $resolutionY >= 1080:
            $quantityFormats = 5;
            $kiloBitrate->push($kb_rate * 0.02);
            $kiloBitrate->push($kb_rate * 0.11);
            $kiloBitrate->push($kb_rate * 0.15);
            $kiloBitrate->push($kb_rate * 0.56);
            $kiloBitrate->push($kb_rate);
            break;
      }

      //if small $kb_rate
      foreach($kiloBitrate as $key => $item) {
         if($item < 250) {
            $kiloBitrate[$key] = 250;
         }
      }

      $available_formats = []; // ['360p','480p','720p','1080p'] formats for saving

      for($i = 0; $i < $quantityFormats; $i++) {
         array_push($available_formats, "{$resolution[$i]}p");
      }

      $dimension = collect([
         function ($filters) {$filters->resize(new Dimension(426, 240));},
         function ($filters) {$filters->resize(new Dimension(640, 360));},
         function ($filters) {$filters->resize(new Dimension(854, 480));},
         function ($filters) {$filters->resize(new Dimension(1280, 720));},
         function ($filters) {$filters->resize(new Dimension(1920, 1080));}
      ]);

      $table = $this->item->table;

      /* ---------------------------------- Video settings end ----------------------------- */


      /* ------------------------------------ MP4 conversion -------------------------------- */
      //$converted_name = $this->getCleanFileName($this->video->path);

      /**
       * Video settings
       *
       * Start compile !!! .mp4 !!!
       *
       * $quantity_format - set video quantity format .mp4 = 6
       * $codec - set audioCodec + videoCodec
       * $kiloBitrate - set rate
       * $videoSetting - set $codec + rate
       * $dimension - set size of window player
       */
      for (
         $min = 0;
         $min < $quantityFormats;
         $min++
      ) {
         $videoSetting = $codec['libx264']->setKiloBitrate($kiloBitrate[$min]);

         if($min < $quantityFormats - 1) {
            FFMpeg::fromDisk($table)
               ->open("{$this->names}/{$this->item->promo_video_url}")

               // add the 'resize' filter...
               ->addFilter($dimension[$min])

               // call the 'export' method...
               ->export()

               // tell the MediaExporter to which disk and in which format we want to export...
               ->toDisk($table)
               ->inFormat($videoSetting) // libx264

               // call the 'save' method with a filename...
               ->save("{$this->names}/{$this->names}_promo_{$resolution[$min]}p.mp4");
         } else {
            rename(
               public_path("media/{$table}/{$this->names}/{$this->item->promo_video_url}"),
               public_path("media/{$table}/{$this->names}/{$this->names}_promo_{$resolution[$min]}p.mp4")
            );
         }
      }
      /* ------------------------------------ MP4 conversion end -------------------------------- */


      /* --------------------------------- webm conversion disable ------------------------------ */
      /**
       * Set video settings for !!! .webm !!!
       */
      //        for (
      //            $min = 0;
      //            $min < $quantityFormats;
      //            $min++
      //        ) {
      //            $videoSetting = $codec['libvpx']->setKiloBitrate($kiloBitrate[$min]);
      //
      //                FFMpeg::fromDisk($table)
      //                    ->open("{$this->names}/{$this->item->promo_video_url}")
      //
      //                    // add the 'resize' filter...
      //                    ->addFilter($dimension[$min])
      //
      //                    // call the 'export' method...
      //                    ->export()
      //
      //                    // tell the MediaExporter to which disk and in which format we want to export...
      //                    ->toDisk($table)
      //                    ->inFormat($videoSetting) // libvpx
      //
      //                    // call the 'save' method with a filename...
      //                    ->save("{$this->names}/{$this->names}_promo_{$resolution[$min]}p.webm");
      //            }
      /* ------------------------------ webm conversion disable end ------------------------------ */


      /*------------------------------------- SAVE INFO -----------------------------------------*/

      // Update the database so we know the convert is done!
      $pathToRoute = "media/{$this->item->table}/{$this->item->route}";

      $tempFile = public_path("{$pathToRoute}/{$this->item->promo_video_url}");
      !File::exists($tempFile)?:unlink($tempFile);

      $this->item->update([
         'promo_video_url' => "{$pathToRoute}/{$this->names}_promo.mp4",
         'converted' => true,
         'promo_video_available_formats' => $available_formats,
         'converted_at' => Carbon::now(),
      ]);
   }
}
