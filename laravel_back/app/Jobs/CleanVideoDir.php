<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class CleanVideoDir implements ShouldQueue
{
   use Dispatchable;
   use InteractsWithQueue;
   use Queueable;
   use SerializesModels;

   private $dirPath;

   /**
    * Remove video files.
    *
    * @param string $dirPath
    */
   public function __construct(string $dirPath)
   {
      $this->dirPath = $dirPath;
   }

   /**
    * Start job for empty video directory.
    *
    * @return void
    */
   public function handle()
   {
      if(!File::exists($this->dirPath)) {
         File::makeDirectory($this->dirPath, 0775, true, true);
      } else {
         File::cleanDirectory($this->dirPath);
      }
   }

   public function failed($exception)
   {
      Log::debug($exception->getMessage());
   }
}
