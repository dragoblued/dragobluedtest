<?php

namespace App\Http\Controllers\Api\Admin;

use App\Stream;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StreamController extends AdminController
{
    public function __construct () {}

    public function findByNameAndKey(string $name, Request $request): JsonResponse
    {
        $stream = Stream::where([
                ['name', $name],
                ['key', $request->get('key')]
            ])
            ->with(['room'])
            ->first();
        if (is_null($stream)) {
            return response()->json('Stream with these name and key has not been found.', 404);
        }
        return response()->json($stream);
    }
}
