<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Jobs\ArchiveVideo;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class CourseControllerTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testArchiveCourseMaterials(): void
    {
        $response = $this->getJson('api/archive/courses/4');

        $response
            ->assertOk();
    }
}
