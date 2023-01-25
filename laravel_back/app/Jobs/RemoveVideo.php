<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Arr;

class RemoveVideo implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private $item;
    private $video;
    private $settings;
    private $names;

    /**
     * Remove video files.
     *
     * @param $item
     * @param $video
     */
    public function __construct($item, $video)
    {
        $this->item = $item;
        $this->video = $video;
        $this->settings = $this->settings($item, $video);
        $this->names = $this->names($this->settings);
    }

    /**
     * Find video folder
     *
     * @param $item
     * @param $video
     * @return array
     */
    private function settings ($item, $video)
    {
        $settings = $item::VIDEOS;
        $settings = Arr::only($settings, $video);
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

        return $settings;
    }

    /**
     * Find video by id or route
     *
     * @param $settings
     * @return string
     */
    private function names($settings): string
    {
        $names = '';
//        foreach ($settings as $value) { // $settings foreach key => value
//            switch ($value->names) {
//                case 'id':
//                    $names = $this->item->id;
//                    break;
//                case 'route':
//                    $names = $this->item->route;
//                    break;
//            }
//        }
          foreach ($settings as $value) { // $settings foreach key => value
             if ($value->names) {
                $names = $this->item[$value->names];
             }
          }
        return $names;
    }

    /**
     * Start job for remove video.
     *
     * @return void
     */
    public function handle()
    {
        foreach ($this->settings as $field => $set) {
            $exist_videos = glob(public_path() . "/media/{$this->item->table}/{$this->names}/{$this->names}*{.mp4,.webm,.m3u8,.ts}", GLOB_BRACE);
            if (count($exist_videos)) {
                foreach ($exist_videos as $exist_video) {
                    unlink($exist_video);
                }
            }

            $exist_videos = glob(public_path() . "/media/{$this->item->table}/{$this->names}/{$this->names}_promo*{.mp4,.webm}", GLOB_BRACE);
            if (count($exist_videos)) {
                foreach ($exist_videos as $exist_video) {
                    unlink($exist_video);
                }
            }
        }
    }
}
