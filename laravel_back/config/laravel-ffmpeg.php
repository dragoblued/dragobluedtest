<?php

return [
    'default_disk' => 'local',

    'ffmpeg' => [
        'binaries' => env('FFMPEG_BINARIES', 'ffmpeg'),
        'threads' => 2
    ],

    'ffprobe' => [
        'binaries' => env('FFPROBE_BINARIES', 'ffprobe')
    ],

    'timeout' => 40000
];
