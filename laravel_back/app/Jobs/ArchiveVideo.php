<?php

namespace App\Jobs;

use Chumper\Zipper\Zipper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;


class ArchiveVideo implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private $lessons;
    private $files;

    /**
     * Archive Video.
     *
     * @param $lessons
     * @param $files
     */
    public function __construct($lessons, $files)
    {
        $this->lessons = $lessons;
        $this->files = $files;
    }

    public function handle(): void
    {
        $lessons = $this->lessons;
        $files = $this->files;

        $path = public_path().'/media/courses/'.$lessons->first()->topic->course->name.'/'.$lessons->first()->topic->course->name.'_materials.zip';

        !File::exists($path)?:unlink($path);

        $zipper = new Zipper;

        $zipper->make($path);

        $files->each(function($file) use ($zipper, $lessons) {
            $zipper->add(public_path()."/{$file}");
        });

        $zipper->close();

//        return response()->download(public_path().'/test.zip', 200, array('content-type' => 'application/zip'));
    }
}
