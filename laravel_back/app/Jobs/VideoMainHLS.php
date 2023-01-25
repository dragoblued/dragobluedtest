<?php

namespace App\Jobs;

use App\User;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;
use FFMpeg;
use FFMpeg\FFProbe;
use App\Classes\UpdateTotalCount;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;

class VideoMainHLS implements ShouldQueue
{
   use Dispatchable;
   use InteractsWithQueue;
   use Queueable;
   use SerializesModels;

   private $item;
   private $name;
   private $srcFilePath;
   private $destFolder;
   private $frameSec;
   private $shouldReplacePoster;
   private $shouldUpdateTotalDuration;
   private $posterPath;
   /* Order here is crucially important */
   private $resolutions = [
      '240p',
      '360p',
      '480p',
      '720p',
      '1080p',
   ];
   /* Order here is crucially important */
   private $formatSelector = [
      // 240p
      0 => [
         'pieces' => '"[0:v]split=1[v1]; [v1]copy[v1out]" ',
         'videoNoMap' => '-filter:v scale=-2:240 ',
         'audioNoMap' => '-b:a 64k ',
         'video' => '-map v:0 -filter:v:0 scale=-2:240 -b:v:0 500k -maxrate:0 560k -bufsize:0 600k ',
         'audio' => '-map a:0 -b:a:0 64k ',
         'pieces-footer' => '"v:0,a:0"'
      ],
      // 360p
      1 => [
         'pieces' => '"[0:v]split=2[v1][v2]; [v1]scale=-2:240[v1out]; [v2]copy[v2out]" ',
         'videoNoMap' => '-filter:v scale=-2:360 ',
         'audioNoMap' => '-b:a 96k ',
         'video' => '-map v:0 -filter:v:1 scale=-2:360 -b:v:1 800k -maxrate:1 856k -bufsize:1 1200k ',
         'audio' => '-map a:0 -b:a:1 96k ',
         'pieces-footer' => '"v:0,a:0 v:1,a:1"'
      ],
      // 480p
      2 => [
         'pieces' => '"[0:v]split=3[v1][v2][v3]; [v1]scale=-2:240[v1out]; [v2]scale=-2:360[v2out]; [v3]copy[v3out]" ',
         'videoNoMap' => '-filter:v scale=-2:480 ',
         'audioNoMap' => '-b:a 128k ',
         'video' => '-map v:0 -filter:v:2 scale=-2:480 -b:v:2 1400k -maxrate:2 1498k -bufsize:2 2100k ',
         'audio' => '-map a:0 -b:a:2 128k ',
         'pieces-footer' => '"v:0,a:0 v:1,a:1 v:2,a:2"'
      ],
      // 720p
      3 => [
         'pieces' => '"[0:v]split=4[v1][v2][v3][v4]; [v1]scale=-2:240[v1out]; [v2]scale=-2:360[v2out]; [v3]scale=-2:480[v3out]; [v4]copy[v4out]" ',
         'videoNoMap' => '-filter:v scale=-2:720 ',
         'audioNoMap' => '-b:a 128k ',
         'video' => '-map v:0 -filter:v:3 scale=-2:720 -b:v:3 2820k -maxrate:3 3550k -bufsize:3 4359k ',
         'audio' => '-map a:0 -b:a:3 128k ',
         'pieces-footer' => '"v:0,a:0 v:1,a:1 v:2,a:2 v:3,a:3"'
      ],
      // 1080p
      4 => [
         'pieces' => '"[0:v]split=5[v1][v2][v3][v4][v5]; [v1]scale=-2:240[v1out]; [v2]scale=-2:360[v2out]; [v3]scale=-2:480[v3out]; [v4]scale=-2:720[v4out]; [v5]copy[v5out]" ',
         'videoNoMap' => '-filter:v scale=-2:1080 ',
         'audioNoMap' => '-b:a 128k ',
         'video' => '-map v:0 -filter:v:4 scale=-2:1080 -b:v:4 5000k -maxrate:4 5350k -bufsize:4 7500k ',
         'audio' => '-map a:0 -b:a:4 192k ',
         'pieces-footer' => '"v:0,a:0 v:1,a:1 v:2,a:2 v:3,a:3 v:4,a:4"'
      ]
   ];
   /**
    * @var mixed
    */


   /**
    * Video Main.
    *
    * @param $item
    * @param string $origVideoField
    * @param string $destFolder
    * @param string $posterField
    * @param bool $shouldReplacePoster
    * @param int $frameSec
    * @param bool $shouldUpdateTotalDuration
    * @throws Exception
    */
   public function __construct($item, string $origVideoField, string $destFolder, string $posterField, $shouldReplacePoster = false, $frameSec = 15, $shouldUpdateTotalDuration = false)
   {
      $this->item = $item;
      $this->name = $this->item[config("admin.{$item->table}.form.{$origVideoField}.names")];
      $this->srcFilePath = public_path($this->item[$origVideoField]);
      $this->destFolder = $destFolder;
      $this->posterPath = $this->item[$posterField];
      $this->shouldReplacePoster = $shouldReplacePoster;
      $this->shouldUpdateTotalDuration = $shouldUpdateTotalDuration;
      $this->frameSec = $frameSec ? (int) $frameSec : 15;

      Log::debug($this->name);
      Log::debug($this->srcFilePath);

      if (!$this->name || !$this->srcFilePath) {
         throw new Exception('Incorrect input data');
      }
   }


   public function handle()
   {
      /* ---------------------------------- Video settings ----------------------------- */
      $ffprobe = FFProbe::create([
         'ffprobe.binaries' => config('laravel-ffmpeg.ffprobe.binaries')
      ]);
      $origVideoDims = $ffprobe
         ->streams($this->srcFilePath)   // extracts streams informations
         ->videos()                      // filters video streams
         ->first()                       // returns the first video stream
         ->getDimensions();              // returns a FFMpeg\Coordinate\Dimension object
      $origVideoW = $origVideoDims->getWidth();
      $origVideoH = $origVideoDims->getHeight();

      $origVideoDur = $ffprobe->format($this->srcFilePath)->get('duration');

      $origVideoCodec = $ffprobe
         ->streams($this->srcFilePath) // extracts streams informations
         ->videos()                      // filters video streams
         ->first()                       // returns the first video stream
         ->get('codec_name');            // returns the codec_name property

      Log::debug($origVideoW);
      Log::debug($origVideoH);
      Log::debug($origVideoDur);
      Log::debug($origVideoCodec);

      $basePath = public_path("media");
      $dest = "{$basePath}/{$this->destFolder}/{$this->name}";
//      $dest = config('filesystems.media-outside')."/{$this->destFolder}/{$this->name}";
      Log::debug($dest);

      /* Check destination directory */
      $this->checkAndCleanDir($dest);


      $quantityFormats = -1;
      switch (true) {
         case $origVideoH >= 240 && $origVideoH < 360:
            $quantityFormats = 0;
            break;
         case $origVideoH >= 360 && $origVideoH < 480:
            $quantityFormats = 1;
            break;
         case $origVideoH >= 480 && $origVideoH < 720:
            $quantityFormats = 2;
            break;
         case $origVideoH >= 720 && $origVideoH < 1080:
            $quantityFormats = 3;
            break;
         case $origVideoH >= 1080:
            $quantityFormats = 4;
            break;
      }

      /* ------------------------------------ Save frame as poster -------------------------------- */
      if (is_null($this->posterPath) || $this->shouldReplacePoster === true) {
         Queue::connection("longFilesJob")->push((new PosterFromFrame(
            $this->item,
            'poster_url',
            'lessons',
            $this->name,
            $this->name,
            '_poster',
            public_path("media/hls/{$this->name}/{$this->name}_{$quantityFormats}.m3u8"),
            $this->frameSec
         )));
      }

      $availableFormats = [];
      /* ------------------------------------ HLS conversion -------------------------------- */
      $mainCmd = config('laravel-ffmpeg.ffmpeg.binaries')." -i {$this->srcFilePath} -threads 2 -c:v libx264 -profile:v main -crf 20 -c:a aac -preset fast -g 48 -sc_threshold 0 -keyint_min 48 ";
      for ($i = 0; $i <= $quantityFormats; $i++) {
         array_push($availableFormats, $this->resolutions[$i]);
         $mainCmd .= $this->formatSelector[$i]['video'];
      }
      for ($i = 0; $i <= $quantityFormats; $i++) {
         $mainCmd .= $this->formatSelector[$i]['audio'];
      }
      $mainCmd .= "-f hls -hls_time 5 -hls_playlist_type vod -hls_flags independent_segments -hls_segment_type mpegts -hls_segment_filename {$dest}/{$this->name}_%v_%02d.ts -master_pl_name {$this->name}.m3u8 -var_stream_map {$this->formatSelector[$quantityFormats]['pieces-footer']} {$dest}/{$this->name}_%v.m3u8 2>&1";


      Log::debug($mainCmd);

      shell_exec($mainCmd);

      /* promo conversion, version 2 */
      for ($i = 0; $i <= $quantityFormats; $i++) {
         $clipCmd = config('laravel-ffmpeg.ffmpeg.binaries')." -ss 0 -i {$this->srcFilePath} -threads 2 -crf 20 -c:a aac -preset fast ";
         if ($i === $quantityFormats) {
            $clipCmd .= "-c:v copy ";
            $clipCmd .= $this->formatSelector[$i]['audioNoMap'];
         } else {
            $clipCmd .= "-c:v libx264 ";
            $clipCmd .= $this->formatSelector[$i]['videoNoMap'];
            $clipCmd .= $this->formatSelector[$i]['audioNoMap'];
         }
         $clipCmd .= "-t 60 ".public_path("media/{$this->item->table}/{$this->name}/{$this->name}_promo_{$this->resolutions[$i]}.mp4");
         Log::debug($clipCmd);
         shell_exec($clipCmd);
      }

      /*------------------------------------- SAVE INFO -----------------------------------------*/
      if ($this->shouldUpdateTotalDuration === true) {
         (new UpdateTotalCount)->updateTotalDuration();
      }

      // update conversion is done!
      !File::exists($this->srcFilePath) ?: unlink($this->srcFilePath);

      /* 0 - video not uploaded
         1 - video mp4 converted
         2 - video hls converted
         3 - video is converting
         4 - convertation errored
      */
      $updateData = [
         'video_url' => null,
         'video_type' => 'm3u8',
         'video_available_formats' => $availableFormats,
         'promo_video_url' => "media/{$this->item->table}/{$this->name}/{$this->name}_promo.mp4",
         'promo_video_duration' => 60,
         'promo_video_available_formats' => $availableFormats,
         'converted' => 2,
         'converted_at' => Carbon::now()
      ];
      if (is_null($this->item->poster_url) || $this->shouldReplacePoster === true) {
         $updateData['poster_url'] = "media/{$this->item->table}/{$this->name}/{$this->name}_poster.jpg";
      }

      $this->item->update($updateData);

      $user = User::find($this->item->user_creator_id);
      if (!is_null($user)) {
         if ($user->hasGroups('VIDEO_CONVERTED_NOTIFIES')) {
            SendCmnEmail::dispatch($user->email, 'Video convertation', 'email.video_converted', $this->item);
         }
      }
   }

   private function checkAndCleanDir ($dir): void
   {
      if(!File::exists($dir)) {
         File::makeDirectory($dir, 0775, true, true);
      } else {
         File::cleanDirectory($dir);
      }
   }

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
