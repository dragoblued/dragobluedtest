<?php


namespace App\Services;


use Illuminate\Support\Str;

class Utilities
{
    public function slug(string $input): string
    {
        return Str::slug($input);
    }
}
