<?php

namespace App\Jobs;

use App\User;
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
use App\Classes\UpdateTotalCount;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;

class VideoMain implements ShouldQueue
{
   use Dispatchable;
   use InteractsWithQueue;
   use Queueable;
   use SerializesModels;

   private $item;
   private $video_extract;
   private $names;
   private $video_url;
   private $frameSec;
   private $shouldReplacePoster;
   private $shouldUpdateTotalDuration;

   /**
    * Video Main.
    *
    * @param $item
    * @param $video_extract
    * @param bool $shouldReplacePoster
    * @param int $frameSec
    * @param bool $shouldUpdateTotalDuration
    */
   public function __construct($item, $video_extract, $shouldReplacePoster = false, $frameSec = 15, $shouldUpdateTotalDuration = false)
   {
      $this->item = $item;
      $this->video_extract = $video_extract;
      $this->shouldReplacePoster = $shouldReplacePoster;
      $this->shouldUpdateTotalDuration = $shouldUpdateTotalDuration;
      $this->names = $this->settings($item);
      $this->video_url = $item->video_url;
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
         //            'libx264' => (new X264('libmp3lame', 'libx264'))->setAudioKiloBitrate(256),
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

      $resolutionY = $this->video_extract['video']['resolution_y']; // Check uploaded resolution file and filter necessary quantity formats

      // 993.89 convert to Kb
      $kb_rate = $this->video_extract['bitrate']/1000;
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
      /* -------------------------------- Video settings end ----------------------------- */

      /* ------------------------------------ MP4 conversion -------------------------------- */
      //$converted_name = $this->getCleanFileName($this->video->path);

      /**
       * start compile !!! .mp4 !!!
       *
       * $quantity_format - set video quantity format .mp4 = 6
       * $codec - set audioCodec + videoCodec
       * $kiloBitrate - set rate
       * $videoSetting - set $codec + rate
       * $dimension - set size of window player
       */
      /* Remove last conversation */
      //        if($this->video_extract['video']['fourcc_lookup'] !== 'H.264/MPEG-4 AVC') {
      /* Remove promo conversation */
      for ($min = 0; $min < $quantityFormats; $min++) {
         $videoSetting = $codec['libx264']
            ->setKiloBitrate($kiloBitrate[$min]);

         /* last format no convert */
         if($min < $quantityFormats - 1) {
            FFMpeg::fromDisk($table)
               ->open("{$this->names}/{$this->video_url}")
               ->addFilter($dimension[$min])
               ->export()
               ->toDisk($table)
               ->inFormat($videoSetting)
               ->save("{$this->names}/{$this->names}_{$resolution[$min]}p.mp4");
         } else {
            rename(
               public_path("media/{$table}/{$this->names}/{$this->video_url}"),
               public_path("media/{$table}/{$this->names}/{$this->names}_{$resolution[$min]}p.mp4")
            );
            /* save 15sec frame as poster */
//            if (is_null($this->item->poster_url) || $this->shouldReplacePoster === true) {
//               Queue::connection("longFilesJob")->push((new PosterFromFrame(
//                  $this->item,
//                  'lessons',
//                  $this->names,
//                  $this->names,
//                  "{$this->names}/{$this->names}_{$resolution[$min]}p.mp4",
//                  $this->frameSec
//               )));
//            }

         }
         /* promo conversion, version 2 */
         FFMpeg::fromDisk($table)
            ->open("{$this->names}/{$this->names}_{$resolution[$min]}p.mp4")
            ->clip(FFMpeg\Coordinate\TimeCode::fromSeconds(0), FFMpeg\Coordinate\TimeCode::fromSeconds(60))
            ->save($videoSetting, public_path("media/{$table}/{$this->names}/{$this->names}_promo_{$resolution[$min]}p.mp4"));
      }


      /* ---------------------- Remove promo conversion, version 1 ---------------------- */
      //        } else {
      //            for (
      //                $min = 0;
      //                $min < $quantityFormats - 1;
      //                $min++
      //            ) {
      //                $videoSetting = $codec['libx264']
      //                    ->setKiloBitrate($kiloBitrate[$min]);
      //
      //                FFMpeg::fromDisk($table)
      //                    ->open("{$this->names}/{$this->video_url}")
      //                    ->addFilter($dimension[$min])
      //                    ->export()
      //                    ->toDisk($table)
      //                    ->inFormat($videoSetting)
      //                    ->save("{$this->names}/{$this->names}_{$resolution[$min]}p.mp4");
      //
      //                FFMpeg::fromDisk($table)
      //                    ->open("{$this->names}/{$this->names}_{$resolution[$min]}p.mp4")
      //                    ->clip(FFMpeg\Coordinate\TimeCode::fromSeconds(0), FFMpeg\Coordinate\TimeCode::fromSeconds(15))
      //                    ->save($videoSetting, public_path("media/{$table}/{$this->names}/{$this->names}_promo_{$resolution[$min]}p.mp4"));

      //                /* save original file */
      //                if($min + 2 === $quantityFormats) {
      //                    File::copy(
      //                        public_path("media/{$table}/{$this->names}/{$this->video_url}"),
      //                        public_path("media/{$table}/{$this->names}/{$this->names}_{$resolution[$min + 1]}p.mp4")
      //                    );
      //
      //                    FFMpeg::fromDisk($table)
      //                        ->open("{$this->names}/{$this->names}_{$resolution[$min + 1]}p.mp4")
      //                        ->clip(FFMpeg\Coordinate\TimeCode::fromSeconds(0), FFMpeg\Coordinate\TimeCode::fromSeconds(15))
      //                        ->save($videoSetting, public_path("media/{$table}/{$this->names}/{$this->names}_promo_{$resolution[$min + 1]}p.mp4"));
      //                }
      //            }
      //        }
      /* ------------------------------- Remove old promo conversion end ----------------------------- */

      /* ------------------------------------- MP4 conversion end ------------------------------------ */




      /*---------------------------------------- WEBM conversion disable ----------------------------------------*/
      //        for (
      //            $min = 0;
      //            $min < $quantityFormats;
      //            $min++
      //        ) {
      //            $videoSetting = $codec['libvpx']->setKiloBitrate($kiloBitrate[$min]);
      //
      //            FFMpeg::fromDisk($table)
      //                ->open("{$this->names}/{$this->video_url}")
      //                ->addFilter($dimension[$min])
      //                ->export()
      //                ->toDisk($table)
      //                ->inFormat($videoSetting)
      //                ->save("{$this->names}/{$this->names}_{$resolution[$min]}p.webm");
      //
      //            FFMpeg::fromDisk($table)
      //                ->open("{$this->names}/{$this->names}_{$resolution[$min]}p.webm")
      //                ->clip(FFMpeg\Coordinate\TimeCode::fromSeconds(0), FFMpeg\Coordinate\TimeCode::fromSeconds(15))
      //                ->save($videoSetting, public_path("media/{$table}/{$this->names}/{$this->names}_promo_{$resolution[$min]}p.webm"));
      //        }
      /*------------------------------------- WEBM conversion disable end -----------------------------------------*/



      /*------------------------------------- SAVE INFO -----------------------------------------*/

      if ($this->shouldUpdateTotalDuration === true) {
         (new UpdateTotalCount)->updateTotalDuration();
      }

      // update conversion is done!
      $pathToRoute = "media/{$this->item->table}/{$this->names}/";

      $tempFile = public_path($pathToRoute.$this->video_url);
      !File::exists($tempFile)?:unlink($tempFile);

      /* 0 - video not uploaded
         1 - video mp4 converted
         2 - video hls converted
         3 - video is converting
         4 - convertation errored
      */
      $updateData = [
         'video_url' => "media/{$this->item->table}/{$this->names}/{$this->names}.mp4",
         'promo_video_url' => "media/{$this->item->table}/{$this->names}/{$this->names}_promo.mp4",
         'video_available_formats' => $available_formats,
         'promo_video_duration' => 60,
         'promo_video_available_formats' => $available_formats,
         'converted' => 1,
         'converted_at' => Carbon::now()
      ];
      if (is_null($this->item->poster_url) || $this->shouldReplacePoster === true) {
         $updateData['poster_url'] = "media/{$this->item->table}/{$this->names}/{$this->names}_poster.jpg";
      }

      $this->item->update($updateData);

      $user = User::find($this->item->user_creator_id);
      if (!is_null($user)) {
         if ($user->hasGroups('VIDEO_CONVERTED_NOTIFIES')) {
            SendCmnEmail::dispatch($user->email, 'Video convertation', 'email.video_converted', $this->item);
         }
      }
   }

   //private function getCleanFileName($filename){
   //    return preg_replace('/\\.[^.\\s]{3,4}$/', '', $filename) . '.mp4';
   //}
   public function failed($exception)
   {
      /* 4 - means error */
      $this->item->update([
         'converted' => 4
      ]);

      $user = User::find($this->item->user_creator_id);
      if (!is_null($user)) {
         if ($user->hasGroups('VIDEO_CONVERTED_NOTIFIES')) {
            SendCmnEmail::dispatch($user->email, 'Video convertation', 'email.video_convertation_failed', $this->item);
         }
      }
      Log::debug($exception->getMessage());
   }
}
