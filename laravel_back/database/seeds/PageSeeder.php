<?php

use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		DB::table('pages')->insert([
			'route'      => 'main',
			'title'      => 'Main',
			'created_at' => now()
		]);
        DB::table('pages')->insert([
            'route'      => 'courses',
            'title'      => 'Courses',
            'created_at' => now()
        ]);
        DB::table('pages')->insert([
            'route'      => 'live-courses',
            'title'      => 'Live Courses',
            'content'    => '{
                "poster_url": "media/gallery/13/13.mp4",
                "poster_available_formats": "[\"360p\",\"480p\",\"720p\",\"1080p\"]"
            }',
            'created_at' => now()
        ]);
        DB::table('pages')->insert([
            'route'      => 'video-courses',
            'title'      => 'Video Courses',
            'created_at' => now()
        ]);
        DB::table('pages')->insert([
            'route'      => 'about',
            'title'      => 'About',
            'content'    => '{
                "poster_url": "media/pages/about/poster.jpg",
                "academic": "D.D.S.&nbsp;Spec in periodontology, PhD",
                "specification": "Periodontologist",
                "iti_affiliation": "ITI Fellow",
                "membership": [
                    {"year": "since <span style=\'font-weight: bold; color: white;\'>2010</span>", "content":"ITI member, 2017 ITI fellow"},
                    {"year": "since <span style=\'font-weight: bold; color: white;\'>2008</span>", "content":"EAO member"},
                    {"year": "since <span style=\'font-weight: bold; color: white;\'>2010</span>", "content":"Baltic Ossoeintegration Academy, Board member"},
                    {"year": "since <span style=\'font-weight: bold; color: white;\'>2010</span>", "content":"Member of Lithuanian Periodontal association"}
                ],
                "positions": ["Periodontologist in private practice <span style=\'font-style: italic;\'>“Vilnius Implantology Center”</span>","Researcher in private research center <span style=\'font-style: italic;\'>“Vilnius research group”</span>"],
                "education": [
                    {"year":"<span style=\'font-weight: bold; color: white;\'>2016</span>","content":"PhD degree at Vilnius University, Lithuania (PhD)"},
                    {"year":"<span style=\'font-weight: bold; color: white;\'>2003 <span style=\'white-space: nowrap;\'>– 2006</span></span>","content":"Post-graduate studies in periodontology at <span style=\'color: white;\'>Kaunas Medical University</span> (Dip perio)"},
                    {"year":"<span style=\'font-weight: bold; color: white;\'>1997 <span style=\'white-space: nowrap;\'>– 2002</span></span>","content":"General practice dentistry studies at Vilnius university (DDS)"}
                ],
                "awards": [
                    "EAO sertification program 2014.",
                    "EAO 2012, Copenhagen, Denmark. Clinical research competition prize <span style=\'color: white;\'>“Crestal bone stability after soft tissue thickening”</span>. Algirdas Puisys, Tomas Linkevicius, Egle Vindasiute, Natalija Maslova, Markus Schlee, Simonas Grybauskas.",
                    "ITI 2010, Geneva, Switzerland. Poster presentation prize. <span style=\'color: white;\'>“The influence of margin location on the amount of undetected cement excess after delivery of cement-retained implant restorations”</span>. Algirdas Puisys, Egle Vindasiute, Tomas Linkevicius, Natalija Maslova."
                ],
                "publications": [
                    {
                        "id": 1,
                        "theme": "<span style=\'font-weight: bold; color: white;\'>“Reaction of crestal bone around implants depending on mucosal tissue thickness: A 1-year prospective clinical study”</span>",
                        "authors": "Algirdas Puisys, Tomas Linkevicius, Peteris Apse, Simonas Grybauskas",
                        "file_url": "media/pages/about/publications/publication1.pdf",
                        "file_ext": "pdf"
                    },
                    {
                        "id": 2,
                        "theme": "<span style=\'font-weight: bold; color: white;\'>“The influence of mucosal tissue thickening on crestal bone stability around bone-level implants. A prospective controlled clinical trial”</span>",
                        "authors": "Algirdas Puisys, Tomas Linkevicius",
                        "file_url": "media/pages/about/publications/publication1.pdf",
                        "file_ext": "pdf"
                    },
                    {
                        "id": 3,
                        "theme": "<span style=\'font-weight: bold; color: white;\'>“Radiological comparison of laser- microtextured and platform-switched implants in thin mucosal biotype”</span>",
                        "authors": "Algirdas Puisys, Tomas Linkevicius, Olga Svediene, Rokas Linkevicius, Laura Linkeviciene",
                        "file_url": "media/pages/about/publications/publication1.pdf",
                        "file_ext": "pdf"
                    },
                    {
                        "id": 4,
                        "theme": "<span style=\'font-weight: bold; color: white;\'>“Influence of Thin Mucosal Tissues on Crestal Bone Stability Around Implants With Platform Switching: A 1-year Pilot Study”</span>",
                        "authors": "Algirdas Puisys, Tomas Linkevicius",
                        "file_url": "media/pages/about/publications/publication1.pdf",
                        "file_ext": "pdf"
                    }
                ]
            }',
            'created_at' => now()
        ]);
        DB::table('pages')->insert([
            'route'      => 'contacts',
            'title'      => 'Contacts',
            'created_at' => now()
        ]);
        DB::table('pages')->insert([
            'route'      => 'privacy-policy',
            'title'      => 'Privacy Policy',
            'created_at' => now()
        ]);
      DB::table('pages')->insert([
         'route'      => 'terms',
         'title'      => 'Terms of Use',
         'created_at' => now()
      ]);
        DB::table('pages')->insert([
            'route'      => 'my-library',
            'title'      => 'My Library',
            'created_at' => now()
        ]);
        DB::table('pages')->insert([
            'route'      => 'basket',
            'title'      => 'Basket',
            'created_at' => now()
        ]);
        DB::table('pages')->insert([
            'route'      => 'successful-payment',
            'title'      => 'Successful Payment',
            'created_at' => now()
        ]);
        DB::table('pages')->insert([
            'route'      => 'auth',
            'title'      => 'Authoriazation',
            'created_at' => now()
        ]);
        DB::table('pages')->insert([
            'route'      => 'account',
            'title'      => 'Account',
            'created_at' => now()
        ]);
        DB::table('pages')->insert([
            'route'      => 'test',
            'title'      => 'Test',
            'created_at' => now()
        ]);
	}
}
