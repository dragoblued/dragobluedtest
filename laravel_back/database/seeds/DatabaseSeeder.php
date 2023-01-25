<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
	/**
	 * Seed the application's database.
	 *
	 * @return void
	 */
	public function run()
	{
		$this->call(PageSeeder::class);
		$this->call(PermissionSeeder::class);
		$this->call(RoleSeeder::class);
		$this->call(GroupSeeder::class);
		$this->call(UserSeeder::class);
		$this->call(ContactSeeder::class);
		$this->call(CourseSeeder::class);
		$this->call(TopicSeeder::class);
		$this->call(SettingSeeder::class);
		$this->call(LessonSeeder::class);
		$this->call(EventSeeder::class);
		$this->call(DateSeeder::class);
		$this->call(TestSeeder::class);
		$this->call(TestQuestionSeeder::class);
		$this->call(GallerySeeder::class);
		$this->call(GalleryPivotSeeder::class);
		$this->call(RoomSeeder::class);
		$this->call(MessageSeeder::class);
		$this->call(UserCourseSeeder::class);
		$this->call(UserTopicSeeder::class);
		$this->call(TicketSeeder::class);
		$this->call(UserGroupSeeder::class);
		$this->call(PromocodeSeeder::class);
		$this->call(UserRoomSeeder::class);
	}
}
