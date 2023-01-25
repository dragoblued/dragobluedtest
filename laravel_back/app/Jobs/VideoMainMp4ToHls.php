<?php

namespace App\Jobs;

use App\User;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class VideoMainMp4ToHls implements ShouldQueue
{
   use Dispatchable;
   use InteractsWithQueue;
   use Queueable;
   use SerializesModels;

   private $item;
   private $formatField;
   private $nameField;
   private $pathFolder;
   private $destFolder;
   private $renameField;

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
      0 => [
         'pieces' => '"[0:v]split=1[v1]; [v1]copy[v1out]" ',
         'video' => '-map v:0 -filter:v:0 scale=-2:240 -b:v:0 500k -maxrate:0 560k -bufsize:0 600k ',
         'audio' => '-map a:0 -b:a:0 64k ',
         'pieces-footer' => '"v:0,a:0"'
      ],
      1 => [
         'pieces' => '"[0:v]split=2[v1][v2]; [v1]scale=-2:240[v1out]; [v2]copy[v2out]" ',
         'video' => '-map v:0 -filter:v:1 scale=-2:360 -b:v:1 800k -maxrate:1 856k -bufsize:1 1200k ',
         'audio' => '-map a:0 -b:a:1 96k ',
         'pieces-footer' => '"v:0,a:0 v:1,a:1"'
      ],
      2 => [
         'pieces' => '"[0:v]split=3[v1][v2][v3]; [v1]scale=-2:240[v1out]; [v2]scale=-2:360[v2out]; [v3]copy[v3out]" ',
         'video' => '-map v:0 -filter:v:2 scale=-2:480 -b:v:2 1400k -maxrate:2 1498k -bufsize:2 2100k ',
         'audio' => '-map a:0 -b:a:2 128k ',
         'pieces-footer' => '"v:0,a:0 v:1,a:1 v:2,a:2"'
      ],
      3 => [
         'pieces' => '"[0:v]split=4[v1][v2][v3][v4]; [v1]scale=-2:240[v1out]; [v2]scale=-2:360[v2out]; [v3]scale=-2:480[v3out]; [v4]copy[v4out]" ',
         'video' => '-map v:0 -filter:v:3 scale=-2:720 -b:v:3 2820k -maxrate:3 3550k -bufsize:3 4359k ',
         'audio' => '-map a:0 -b:a:3 128k ',
         'pieces-footer' => '"v:0,a:0 v:1,a:1 v:2,a:2 v:3,a:3"'
      ],
      4 => [
         'pieces' => '"[0:v]split=5[v1][v2][v3][v4][v5]; [v1]scale=-2:240[v1out]; [v2]scale=-2:360[v2out]; [v3]scale=-2:480[v3out]; [v4]scale=-2:720[v4out]; [v5]copy[v5out]" ',
         'video' => '-map v:0 -filter:v:4 scale=-2:1080 -b:v:4 5000k -maxrate:4 5350k -bufsize:4 7500k ',
         'audio' => '-map a:0 -b:a:4 192k ',
         'pieces-footer' => '"v:0,a:0 v:1,a:1 v:2,a:2 v:3,a:3 v:4,a:4"'
      ]
   ];

   /**
    * Video Main.
    *
    * @param $item
    * @param $formatField
    * @param $nameField
    * @param $pathFolder
    * @param $destFolder
    */
   public function __construct($item, string $formatField, string $nameField, string $pathFolder,
                               string $destFolder, string $renameField = null)
   {
      $this->item = $item;
      $this->formatField = $formatField;
      $this->nameField = $nameField;
      $this->pathFolder = $pathFolder;
      $this->destFolder = $destFolder;
      $this->renameField = $renameField;
   }

   public function handle()
   {
      $formats = $this->item[$this->formatField];
      $name = $this->item[$this->nameField];
      $basePath = public_path("media");

      if (!is_array($formats) || !$name || !$basePath) {
         throw new Exception('Incorrect input data');
      }

      /* Check destination directory */
      $this->checkOrMakeDir("{$basePath}/{$this->destFolder}/{$name}");

      /* ------------------------------------ MP4 TO HLS conversion -------------------------------- */
      $topFormat = $formats[count($formats) - 1];
      $quantityFormats = array_search($topFormat, $this->resolutions);
      $srcFilePath = "{$basePath}/{$this->pathFolder}/{$name}/{$name}_{$topFormat}.mp4";
      $dest = "{$basePath}/{$this->destFolder}/{$name}";

      if (!File::exists($srcFilePath)) {
         throw new Exception('Top Format File does not exists. '.$srcFilePath);
      }
      $availableFormats = [];

      $cmd = config('laravel-ffmpeg.ffmpeg.binaries')." -i {$srcFilePath} -threads 2 -c:v libx264 -profile:v main -crf 20 -c:a aac -preset fast -g 48 -sc_threshold 0 -keyint_min 48 ";
//      $cmd .= $this->formatSelector[$topFormat]['pieces'];
      //         $cmd = config('laravel-ffmpeg.ffmpeg.binaries')." -i {$srcFilePath} -threads 2 -hls_time 5 -hls_list_size 0  -f hls {$dest} 2>&1";
      for ($i = 0; $i <= $quantityFormats; $i++) {
         array_push($availableFormats, $this->resolutions[$i]);
         $cmd .= $this->formatSelector[$i]['video'];
      }
      for ($i = 0; $i <= $quantityFormats; $i++) {
         $cmd .= $this->formatSelector[$i]['audio'];
      }
      $cmd .= "-f hls -hls_time 5 -hls_playlist_type vod -hls_flags independent_segments -hls_segment_type mpegts -hls_segment_filename {$dest}/{$name}_%v_%03d.ts -master_pl_name {$name}.m3u8 -var_stream_map {$this->formatSelector[$quantityFormats]['pieces-footer']} {$dest}/{$name}_%v.m3u8 2>&1";
      Log::debug($cmd);

      shell_exec($cmd);


      /*------------------------------------- UPDATE -----------------------------------------*/
      /* 0 - video not uploaded
         1 - video mp4 converted
         2 - video hls converted
         3 - video is converting
         4 - convertation errored
      */
      $updateData = [
         $this->formatField => $availableFormats,
         'converted' => 2,
         'converted_at' => Carbon::now()
      ];
      if ($this->renameField) {
         $updateData[$this->renameField] = "media/{$this->destFolder}/{$name}/{$name}.m3u8";
      }
      $this->item->update($updateData);

      $user = User::find($this->item->user_creator_id);
      if (!is_null($user)) {
         if ($user->hasGroups('VIDEO_CONVERTED_NOTIFIES')) {
            SendCmnEmail::dispatch($user->email, 'Video convertation', 'email.video_converted', $this->item);
         }
      }
   }

   private function checkOrMakeDir ($dir): void
   {
      if(!File::exists($dir)) {
         File::makeDirectory($dir, 0775, true, true);
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
