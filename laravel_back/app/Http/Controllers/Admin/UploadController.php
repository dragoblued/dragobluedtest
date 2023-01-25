<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class UploadController extends Controller
{
	public function uploader (Request $request)
	{

//		$request->validate([ 'files.*' => 'image' ]);

		$files = $_FILES['files'];
		$folder = $request->folder;
		$folderPath = public_path($folder);
		$info = (object) [
			'files'    => [],
			'isImages' => [],
		];
      if(!File::exists($folderPath)) {
         File::makeDirectory($folderPath, 0775, true, true);
      }
		foreach ($files['name'] as $i => $file) {
			// генерация имени
			do {
				$fileName = strtolower(str_random(10)) .'.';
				$fileName .= pathinfo($file, PATHINFO_EXTENSION);
			}
			while(File::exists(public_path($folder . $fileName)));

			// загружаем изображение
			move_uploaded_file($files['tmp_name'][$i], $folderPath.$fileName);

			// Compress image
         \Tinify\setKey("RfmssKKdFbbTwkVHMkz3jqjpmj9kG6QM");
         $source = \Tinify\fromFile($folderPath.$fileName);
         $source->toFile($folderPath.$fileName);

			// формируем ответ
			$info->files[$i] = $fileName;
			$info->isImages[$i] = true;
		}

		$response = (object) [
			'success' => true,
			'time' => date('Y-m-d H:i:s'),
			'data' => (object) [
				'baseurl'  => "/{$folder}",
				'messages' => [],
				'files'    => $info->files,
				'isImages' => $info->isImages,
				'code'     => 220,
			],
			// 'debug' => $a,
		];

		return json_encode($response);
	}

	public function browser ()
	{
      Log::debug('browse');
	}
}
