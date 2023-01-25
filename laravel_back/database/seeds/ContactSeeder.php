<?php

use Illuminate\Database\Seeder;

class ContactSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		DB::table('contacts')->insert([
			'active'     => true,
			'name'     	 => 'Alex',
			'phone'      => '+79999999999',
			'created_at' => now()
		]);
	}
}
