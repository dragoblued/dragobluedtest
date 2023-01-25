<?php

use Illuminate\Database\Seeder;

class GalleryPivotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ([10,3,11,12] as $id) {
            DB::table('gallery_pivot')->insert([
                'gallery_id' => $id,
                'page_id'    => 3,
                'created_at' => now()
            ]);
            foreach ([1,2,3,4,5] as $courseId) {
                DB::table('gallery_pivot')->insert([
                    'gallery_id' => $id,
                    'event_id'   => $courseId,
                    'created_at' => now()
                ]);
            }
        }
        foreach ([1,2,4,5,6,7,8] as $num) {
            DB::table('gallery_pivot')->insert([
                'gallery_id' => $num,
                'page_id'    => 5,
                'created_at' => now()
            ]);
        }
    }
}
