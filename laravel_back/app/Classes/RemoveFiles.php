<?php


namespace App\Classes;


use Illuminate\Support\Facades\File;

class RemoveFiles
{
    protected  $item;
    protected  $names;

    public function __construct($item)
    {
        $this->item = $item;
        $this->names = $this->settings($item);
    }

    /**
     * @param $item
     * @return string
     */
    public function settings ($item): string
    {
        // Settings
        $settings = $item::VIDEOS;
        $itemTable = str_replace('_','.',$item->table);
        $form = config("admin.{$itemTable}.form");

        foreach ($settings as $field => $folder) {
            if(isset($form[$field])){
                $set = $form[$field];
                unset($set['type'], $set['signature']);
                $settings[$field] = (object) array_merge(
                    [ 'folder' => $folder ],
                    $set
                );
            }
        }

         foreach ($settings as $value) { // $settings foreach key => value
          
          if (isset($value->names)) {
             $names = $this->item[$value->names];
          }

         }
        return $names;
    }

    public function RemoveVideo(): void
    {
        $exist_videos = glob(public_path()."/media/{$this->item->table}/{$this->names}/*{.mp4,.webm}", GLOB_BRACE);
        if(count($exist_videos)) {
            foreach($exist_videos as $exist_video) {
                unlink($exist_video);
            }
        }
    }

    public function RemoveDirectory()
    {
        $path = public_path()."/media/{$this->item->table}/{$this->names}/";
        if(File::isDirectory($path)) {
            File::deleteDirectory($path);
        }
    }
}
