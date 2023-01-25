<?php

namespace App\Http\Controllers\Api;

use App\Gallery;
use App\Page;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class PageController extends Controller
{
    protected $model = Page::class;

    public function show($uniqueField): JsonResponse
    {
       $item = $this->model::where('route', $uniqueField)
          ->with(['gallery'])
          ->firstOrFail();
       switch ($item->id) {
          case 1:
             $data = json_decode($item->content, true);
             if ($data) {
                $data['middle_video_item'] = Gallery::find($data['middle_video_url']);
                $item->content = $data;
             }
             break;
          case 3:
             $data = json_decode($item->content, true);
             if ($data) {
                $data['header_video_item'] = Gallery::find($data['header_video_url']);
                $item->content = $data;
             }
             break;
          default:
             break;
       }
       return response()->json($item);
    }
}
