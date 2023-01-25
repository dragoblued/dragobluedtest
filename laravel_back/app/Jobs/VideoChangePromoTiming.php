<?php

namespace App\Jobs;

use App\Lesson;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\Format\Video\X264;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use FFMpeg as FFMpeg;
use Illuminate\Support\Facades\Queue;
use Owenoj\LaravelGetId3\GetId3;

class VideoChangePromoTiming implements ShouldQueue
{
   use Dispatchable;
   use InteractsWithQueue;
   use Queueable;
   use SerializesModels;

   private $item;
   private $newTiming;
   private $onlyPosters;
   private $frameSec;
   private $lessonPathsArr;

   /**
    * Video Change Promo Timing.
    *
    * @param $item
    * @param $newTiming
    * @param $onlyPosters
    * @param int $frameSec
    */
   public function __construct($item, $newTiming, $onlyPosters, $frameSec = 15)
   {
      $this->item = $item;
      $this->newTiming = $newTiming;
      $this->onlyPosters = $onlyPosters;
      $this->lessonPathsArr = $this->getPaths($this->item);
      $this->frameSec = $frameSec ? (int) $frameSec : 15;
   }

   /**
    * Find video folder
    *
    * @param $item
    * @return string
    */
   private function getPaths ($item): array
   {
      $paths = [];
      $lessons = $item->lessons;
      foreach ($lessons as $lesson) {
         $info = [];
         if ($lesson->video_url && is_array($lesson->video_available_formats) && $lesson->converted === 1) {
            $info = [
               'id' => $lesson->id,
               'route' => $lesson->route,
               'formats' => $lesson->video_available_formats
            ];
         }
         if (count($info) > 0) {
            array_push($paths, $info);
         }
      }
      return $paths;
   }

   public function handle()
   {
      $table = 'lessons';

      /* promo cutting */
      foreach ($this->lessonPathsArr as $lessonInfo) {
         $lesson = Lesson::findOrFail($lessonInfo['id']);
         $route = $lessonInfo['route'];

         $formatsCount = count($lessonInfo['formats']);
         $track = new GetId3(public_path("media/{$table}/{$route}/{$route}_{$lessonInfo['formats'][$formatsCount - 1]}.mp4")); // video seconds get GetId3 package
         $video_extract = $track->extractInfo();
         $kb_rate = $video_extract['bitrate']/1000;
         $kiloBitrate = collect([]);
         switch ($formatsCount) {
            case 0:
               break;
            case 1:
               $kiloBitrate->push($kb_rate);
               break;
            case 2:
               $kiloBitrate->push($kb_rate * 0.17);
               $kiloBitrate->push($kb_rate);
               break;
            case 3:
               $kiloBitrate->push($kb_rate * 0.13);
               $kiloBitrate->push($kb_rate * 0.75);
               $kiloBitrate->push($kb_rate);
               break;
            case 4:
               $kiloBitrate->push($kb_rate * 0.03);
               $kiloBitrate->push($kb_rate * 0.20);
               $kiloBitrate->push($kb_rate * 0.26);
               $kiloBitrate->push($kb_rate);
               break;
            case 5:
               $kiloBitrate->push($kb_rate * 0.02);
               $kiloBitrate->push($kb_rate * 0.11);
               $kiloBitrate->push($kb_rate * 0.15);
               $kiloBitrate->push($kb_rate * 0.56);
               $kiloBitrate->push($kb_rate);
               break;
         }

         foreach ($lessonInfo['formats'] as $index => $format) {
            Log::debug(public_path("media/{$table}/{$route}/{$route}_{$format}.mp4"));
            if (File::exists(public_path("media/{$table}/{$route}/{$route}_{$format}.mp4"))) {

               /* Если присутсвует настройка onlyPosters === true, пропускаем переконвертирование promo */
               if ($this->onlyPosters === false) {
//                  $this->removePromoFile(public_path("media/{$table}/{$route}/{$route}_promo_{$format}.mp4"));

                  $videoSetting = (new X264('aac', 'libx264'))
                     ->setAudioKiloBitrate(256)
                     ->setKiloBitrate($kiloBitrate[$index]);

                  FFMpeg::fromDisk($table)
                     ->open("{$route}/{$route}_{$format}.mp4")
                     ->clip(TimeCode::fromSeconds(0), TimeCode::fromSeconds(60))
                     ->save($videoSetting, public_path("media/{$table}/{$route}/{$route}_promo_{$format}.mp4"));
                  $lesson->promo_video_duration = 60;
                  $lesson->save();
               }

               if (($index === (count($lessonInfo['formats'])- 1)) && is_null($lesson->poster_url)) {
//                  Queue::connection("longFilesJob")->push((new PosterFromFrame(
//                     $lesson,
//                     'lessons',
//                     $route,
//                     $route,
//                     "{$route}/{$route}_{$format}.mp4",
//                     $this->frameSec
//                  )));
               }

            }
         }
      }
   }

   private function removePromoFile($path) {
      if(File::exists($path)){
         File::delete($path);
      }
   }
}
