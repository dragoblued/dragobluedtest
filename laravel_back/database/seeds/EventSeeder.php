<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;

class EventSeeder extends Seeder
{
    /**
     * Run the datesbase seeds.
     *
     * @return void
     */
    public function run()
    {
       $env = Config::get('app.env');
//       $env = 'development';
       if ($env !== 'local') {
          /* Site Seeds */
          $file = database_path('seeds/site_seeds/events.json');
          $data = File::get($file);

          $data = json_decode($data)[2]->data;

          foreach ($data as $value) {
             DB::table('events')->insert(
                ((array) $value)
             );
          }
       } else {
          /* Local Seeds */
          DB::table('events')->insert([
             'route' => 'basic',
             'name' => 'basic',
             'title' => 'Basic',
             'sub_title' => 'My first implant. How to start with confidence',
             'description' => 'During the course we are going to focus on mandible posterior part; the different need of augmentations; lateral and vertical GBR; importance of soft tissue thickness, keratinized and attached gingiva around implants.',
             'duration' => 1,
             'langs' => '["Lithuanian","English","Russian"]',
             'plan' => '[
                "Anatomy of defects",
                "Immediate, flapless and regular implant placement in mandible",
                "Materials for bone augmentation",
                "Guided bone regeneration (GBR) in posterior mandible",
                "Vertical bone augmentation",
                "Keratinized and attached gingiva around implants"
            ]',
             'program' => '[
                [
                    {"start":"10:00","end":"10:30","label":"Implants in posterior mandible"},
                    {"start":"10:30","end":"11:30","label":"Immediate, flapless, regular implant placement"},
                    {"start":"11:30","end":"12:00","label":"Coffee break"},
                    {"start":"12:00","end":"13:30","label":"Guided bone regeneration (GBR)"},
                    {"start":"13:30","end":"14:30","label":"Lunch"},
                    {"start":"14:30","end":"15:30","label":"Guided bone regeneration (GBR) – HANDS ON"},
                    {"start":"15:30","end":"18:00","label":"Practice on pig jaw – HANDS ON "}
                ]
            ]',
             'poster_url' => 'media/events/module1/module1_poster.jpg',
             'collage_url' => 'media/events/module1/module1_collage.png',
             'promo_video_url' => 'media/events/module2/module2_promo.mp4',
             'promo_video_available_formats' => '[720p,480p,360p]',
             'address' => 'Polocko St. 21 / Žvirgždyno St. 1, Vilnius',
             'address_building_name' => 'Vilniaus implantologijos centro klinika',
             'address_coordinates' => '[25.270462,54.679921]',
             'actual_price' => 900,
             'discount_price' => 800,
             'created_at' => now()
          ]);
          DB::table('events')->insert([
             'route' => 'module1',
             'name' => 'module1',
             'title' => 'Module 1',
             'sub_title' => 'Bone and Soft Tissue Augmentation',
             'subsign' => 'Posterior mendible',
             'description' => 'During the course we are going to focus on mandible posterior part; the different need of augmentations; lateral and vertical GBR; importance of soft tissue thickness, keratinized and attached gingiva around implants.',
             'duration' => 2,
             'langs' => '["Lithuanian","English","Russian"]',
             'plan' => '[
                "Anatomy of defects",
                "Immediate, flapless and regular implant placement in mandible",
                "Materials for bone augmentation",
                "Guided bone regeneration (GBR) in posterior mandible",
                "Vertical bone augmentation",
                "Keratinized and attached gingiva around implants"
            ]',
             'program' => '[
                [
                    {"start":"10:00","end":"10:30","label":"Implants in posterior mandible"},
                    {"start":"10:30","end":"11:30","label":"Immediate, flapless, regular implant placement"},
                    {"start":"11:30","end":"12:00","label":"Coffee break"},
                    {"start":"12:00","end":"13:30","label":"Guided bone regeneration (GBR)"},
                    {"start":"13:30","end":"14:30","label":"Lunch"},
                    {"start":"14:30","end":"15:30","label":"Guided bone regeneration (GBR) – HANDS ON"},
                    {"start":"15:30","end":"18:00","label":"Practice on pig jaw – HANDS ON "}
                ],
                [
                    {"start":"10:00","end":"10:30","label":"Implants in posterior mandible"},
                    {"start":"10:30","end":"11:30","label":"Immediate, flapless, regular implant placement"},
                    {"start":"11:30","end":"12:00","label":"Coffee break"},
                    {"start":"12:00","end":"13:30","label":"Guided bone regeneration (GBR)"},
                    {"start":"13:30","end":"14:30","label":"Lunch"},
                    {"start":"14:30","end":"15:30","label":"Guided bone regeneration (GBR) – HANDS ON"},
                    {"start":"15:30","end":"18:00","label":"Practice on pig jaw – HANDS ON "}
                ]
            ]',
             'poster_url' => 'media/events/module2/module2_poster.jpg',
             'collage_url' => 'media/events/module2/module2_collage.png',
             'promo_video_url' => 'media/events/module2/module2_promo.mp4',
             'promo_video_available_formats' => '[720p,480p,360p]',
             'address' => 'Polocko St. 21 / Žvirgždyno St. 1, Vilnius',
             'address_building_name' => 'Vilniaus implantologijos centro klinika',
             'address_coordinates' => '[25.270462,54.679921]',
             'actual_price' => 1000,
             'discount_price' => 800,
             'created_at' => now()
          ]);
          DB::table('events')->insert([
             'route' => 'module2',
             'name' => 'module2',
             'title' => 'Module 2',
             'sub_title' => 'Implants in Aesthetic Area',
             'description' => 'During the course we are going to focus on mandible posterior part; the different need of augmentations; lateral and vertical GBR; importance of soft tissue thickness, keratinized and attached gingiva around implants.',
             'duration' => 1,
             'langs' => '["Lithuanian","English","Russian"]',
             'plan' => '[
                "Anatomy of defects",
                "Immediate, flapless and regular implant placement in mandible",
                "Materials for bone augmentation",
                "Guided bone regeneration (GBR) in posterior mandible",
                "Vertical bone augmentation",
                "Keratinized and attached gingiva around implants"
            ]',
             'program' => '[
                [
                    {"start":"10:00","end":"10:30","label":"Implants in posterior mandible"},
                    {"start":"10:30","end":"11:30","label":"Immediate, flapless, regular implant placement"},
                    {"start":"11:30","end":"12:00","label":"Coffee break"},
                    {"start":"12:00","end":"13:30","label":"Guided bone regeneration (GBR)"},
                    {"start":"13:30","end":"14:30","label":"Lunch"},
                    {"start":"14:30","end":"15:30","label":"Guided bone regeneration (GBR) – HANDS ON"},
                    {"start":"15:30","end":"18:00","label":"Practice on pig jaw – HANDS ON "}
                ]
            ]',
             'poster_url' => 'media/events/module3/module3_poster.jpg',
             'collage_url' => 'media/events/module3/module3_collage.png',
             'promo_video_url' => 'media/events/module2/module2_promo.mp4',
             'promo_video_available_formats' => '[720p,480p,360p]',
             'address' => 'Polocko St. 21 / Žvirgždyno St. 1, Vilnius',
             'address_building_name' => 'Vilniaus implantologijos centro klinika',
             'address_coordinates' => '[25.270462,54.679921]',
             'actual_price' => 1000,
             'discount_price' => 800,
             'created_at' => now()
          ]);
          DB::table('events')->insert([
             'route' => 'module3',
             'name' => 'module3',
             'title' => 'Module 3',
             'sub_title' => 'Bone and Soft Tissue Augmentation',
             'description' => 'During the course we are going to focus on mandible posterior part; the different need of augmentations; lateral and vertical GBR; importance of soft tissue thickness, keratinized and attached gingiva around implants.',
             'duration' => 2,
             'langs' => '["Lithuanian","English","Russian"]',
             'plan' => '[
                "Anatomy of defects",
                "Immediate, flapless and regular implant placement in mandible",
                "Materials for bone augmentation",
                "Guided bone regeneration (GBR) in posterior mandible",
                "Vertical bone augmentation",
                "Keratinized and attached gingiva around implants"
            ]',
             'program' => '[
                [
                    {"start":"10:00","end":"10:30","label":"Implants in posterior mandible"},
                    {"start":"10:30","end":"11:30","label":"Immediate, flapless, regular implant placement"},
                    {"start":"11:30","end":"12:00","label":"Coffee break"},
                    {"start":"12:00","end":"13:30","label":"Guided bone regeneration (GBR)"},
                    {"start":"13:30","end":"14:30","label":"Lunch"},
                    {"start":"14:30","end":"15:30","label":"Guided bone regeneration (GBR) – HANDS ON"},
                    {"start":"15:30","end":"18:00","label":"Practice on pig jaw – HANDS ON "}
                ],
                [
                    {"start":"10:00","end":"10:30","label":"Implants in posterior mandible"},
                    {"start":"10:30","end":"11:30","label":"Immediate, flapless, regular implant placement"},
                    {"start":"11:30","end":"12:00","label":"Coffee break"},
                    {"start":"12:00","end":"13:30","label":"Guided bone regeneration (GBR)"},
                    {"start":"13:30","end":"14:30","label":"Lunch"},
                    {"start":"14:30","end":"15:30","label":"Guided bone regeneration (GBR) – HANDS ON"},
                    {"start":"15:30","end":"18:00","label":"Practice on pig jaw – HANDS ON "}
                ]
            ]',
             'poster_url' => 'media/events/module4/module4_poster.jpg',
             'collage_url' => 'media/events/module4/module4_collage.png',
             'promo_video_url' => 'media/events/module2/module2_promo.mp4',
             'promo_video_available_formats' => '[720p,480p,360p]',
             'address' => 'Polocko St. 21 / Žvirgždyno St. 1, Vilnius',
             'address_building_name' => 'Vilniaus implantologijos centro klinika',
             'address_coordinates' => '[25.270462,54.679921]',
             'actual_price' => 1000,
             'discount_price' => 800,
             'created_at' => now()
          ]);
          DB::table('events')->insert([
             'route' => 'peri-implantitis',
             'name' => 'peri-implantitis',
             'title' => 'Peri-implantitis',
             'sub_title' => 'Prevention and treatment',
             'description' => 'During the course we are going to focus on mandible posterior part; the different need of augmentations; lateral and vertical GBR; importance of soft tissue thickness, keratinized and attached gingiva around implants.',
             'duration' => 2,
             'langs' => '["Lithuanian","English","Russian"]',
             'plan' => '[
                "Anatomy of defects",
                "Immediate, flapless and regular implant placement in mandible",
                "Materials for bone augmentation",
                "Guided bone regeneration (GBR) in posterior mandible",
                "Vertical bone augmentation",
                "Keratinized and attached gingiva around implants"
            ]',
             'program' => '[
                [
                    {"start":"10:00","end":"10:30","label":"Implants in posterior mandible"},
                    {"start":"10:30","end":"11:30","label":"Immediate, flapless, regular implant placement"},
                    {"start":"11:30","end":"12:00","label":"Coffee break"},
                    {"start":"12:00","end":"13:30","label":"Guided bone regeneration (GBR)"},
                    {"start":"13:30","end":"14:30","label":"Lunch"},
                    {"start":"14:30","end":"15:30","label":"Guided bone regeneration (GBR) – HANDS ON"},
                    {"start":"15:30","end":"18:00","label":"Practice on pig jaw – HANDS ON "}
                ],
                [
                    {"start":"10:00","end":"10:30","label":"Implants in posterior mandible"},
                    {"start":"10:30","end":"11:30","label":"Immediate, flapless, regular implant placement"},
                    {"start":"11:30","end":"12:00","label":"Coffee break"},
                    {"start":"12:00","end":"13:30","label":"Guided bone regeneration (GBR)"},
                    {"start":"13:30","end":"14:30","label":"Lunch"},
                    {"start":"14:30","end":"15:30","label":"Guided bone regeneration (GBR) – HANDS ON"},
                    {"start":"15:30","end":"18:00","label":"Practice on pig jaw – HANDS ON "}
                ]
            ]',
             'poster_url' => 'media/events/module5/module5_poster.jpg',
             'collage_url' => 'media/events/module5/module5_collage.png',
             'promo_video_url' => 'media/events/module2/module2_promo.mp4',
             'promo_video_available_formats' => '[720p,480p,360p]',
             'address' => 'Polocko St. 21 / Žvirgždyno St. 1, Vilnius',
             'address_building_name' => 'Vilniaus implantologijos centro klinika',
             'address_coordinates' => '[25.270462,54.679921]',
             'actual_price' => 1100,
             'discount_price' => 1000,
             'created_at' => now()
          ]);
       }
    }
}
