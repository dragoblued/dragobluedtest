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
use Illuminate\Support\Facades\Queue;

class VideoGallery implements ShouldQueue
{
   use Dispatchable;
   use InteractsWithQueue;
   use Queueable;
   use SerializesModels;

   private $item;
   private $params;
   private $bitrate;
   private $names;
   private $frameSec;
   private $shouldReplacePoster;

   /**
    * Video Gallery.
    *
    * @param $item
    * @param $params
    * @param $bitrate
    * @param bool $shouldReplacePoster
    * @param int $frameSec
    */
   public function __construct($item, $params, $bitrate, $shouldReplacePoster = false, $frameSec = 15)
   {
      $this->item = $item;
      $this->params = $params;
      $this->bitrate = $bitrate;
      $this->names = $this->settings($item);
      $this->shouldReplacePoster = $shouldReplacePoster;
      $this->frameSec = $frameSec ? (int) $frameSec : 15;
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



   public function handle()
   {
      /* ---------------------------------- Video settings ----------------------------- */
      /**
       * settings video formatter
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

      $resolutionY = $this->params['resolution_y']; // Check uploaded resolution file and filter necessary quantity formats

      // 993.89 convert to Kb
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

      //if $kb_rate is small
      foreach($kiloBitrate as $key => $item) {
         if($item < 250) {
            $kiloBitrate[$key] = 250;
         }
      }

      $available_formats = collect([]); // ['360p','480p','720p','1080p'] formats for saving
      for($i = 0; $i < $quantityFormats; $i++) {
         $available_formats->push("{$resolution[$i]}p");
         //            $available_formats->push('"'."{$resolution[$i]}p".'"');
      }
      $available_formats = $available_formats->toArray();
      //        $available_formats = '['.implode(",", $available_formats->toArray()).']';

      $dimension = collect([
         function ($filters) {$filters->resize(new Dimension(426, 240));},
         function ($filters) {$filters->resize(new Dimension(640, 360));},
         function ($filters) {$filters->resize(new Dimension(854, 480));},
         function ($filters) {$filters->resize(new Dimension(1280, 720));},
         function ($filters) {$filters->resize(new Dimension(1920, 1080));}
      ]);

      $table = $this->item->table;

      /* ---------------------------------- Video settings end ----------------------------- */



      /* ------------------------------------- MP4 conversion -------------------------------- */

      //$converted_name = $this->getCleanFileName($this->video->path);

      // <- video settings

      /**
       * start compile !!! .mp4 !!!
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

         /* last format no convert */
         if($min < $quantityFormats - 1) {
            FFMpeg::fromDisk($table)
               ->open("{$this->names}/{$this->item->url}")

               // add the 'resize' filter...
               ->addFilter($dimension[$min])

               // call the 'export' method...
               ->export()

               // tell the MediaExporter to which disk and in which format we want to export...
               ->toDisk($table)
               ->inFormat($videoSetting) // libx264

               // call the 'save' method with a filename...
               ->save("{$this->names}/{$this->names}_{$resolution[$min]}p.mp4");
         } else {
            rename(
               public_path("media/{$table}/{$this->names}/{$this->item->url}"),
               public_path("media/{$table}/{$this->names}/{$this->names}_{$resolution[$min]}p.mp4")
            );
            /* save 15sec frame as poster */
            if (is_null($this->item->poster_url) || $this->shouldReplacePoster === true) {
//               Queue::connection("longFilesJob")->push((new PosterFromFrame(
//                  $this->item,
//                  'gallery',
//                  $this->names,
//                  $this->names,
//                  "{$this->names}/{$this->names}_{$resolution[$min]}p.mp4",
//                  $this->frameSec
//               )));
            }
         }
      }
      /* ------------------------------------- MP4 conversion end -------------------------------- */


      /*--------------------------------- SAVE Video INFO ---------------------------------------*/

      // update the database so we know the convert is done!
      $pathToRoute = "media/{$this->item->table}/{$this->names}/";

      $tempFile = public_path($pathToRoute.$this->item->url);
      !File::exists($tempFile)?:unlink($tempFile);

      $this->item->update([
         'url' => "{$pathToRoute}{$this->names}.mp4",
         'converted' => 1,
         'available_formats' => $available_formats,
      ]);
   }
}
