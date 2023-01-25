<?php


namespace App\Classes;

use App\Course;
use App\Date;
use App\Lesson;
use App\Ticket;
use App\Topic;
use Illuminate\Support\Facades\Log;

class UpdateTotalCount
{
   /*
    * Count Topics and Courses indicators
    *
    * Update App\Models\Topic lessons_count
    * Update App\Models\Course lessons_count
    */
   public function updateTotalLessons(int $id = null): void
   {
      $id = $id ?? null;
      $langs = [];
      switch ($id) {
         case true:
            $lesson = Lesson::with(['topic'])->find($id);
            if(!is_null($lesson->topic)) {
               $topic = $lesson->topic->load('course');
               $topic->lessons_count--;

               // Обновление списка языков для топика
               $langs = $topic->lessons()->get()->unique('lang')->map(function ($lesson) {
                  return $lesson->lang;
               })->toArray();
               $langs = array_filter($langs);
               sort($langs);
               $topic->lang = $langs;

               $topic->save();
               if(!is_null($lesson->topic->course)) {
                  $course = $lesson->topic->course;

                  // Обновление списка языков для курса
                  $langs = $course->topics()->get()->unique('lang')->map(function ($topic) {
                     return $topic->lang;
                  })->toArray();
                  if($langs){
                     $langs = array_unique(array_merge(...$langs));
                  }
                  sort($langs);
                  $course->lang = $langs;

                  $course->lessons_count--;
                  $course->save();
               }
            }
            break;

         case false:
            // App\Models\Topic lessons_count
            $topics = Topic::with('lessons')->get();
            $topics->each(function($topic) {
               $topic->lessons_count = $topic->lessons->count();

               // Обновление списка языков для топика
               $langs = $topic->lessons()->get()->unique('lang')->map(function ($lesson) {
                  return $lesson->lang;
               })->toArray();
               $langs = array_filter($langs);
               sort($langs);
               $topic->lang = $langs;

               $topic->save();
            });
            // App\Models\Course lessons_count
            $courses = Course::with('lessons')->get();
            $courses->each(function($course) {
               $course->lessons_count = $course->lessons->count();

               // Обновление списка языков для курса
               $langs = $course->topics()->get()->unique('lang')->map(function ($topic) {
                  return $topic->lang;
               })->toArray();
               if($langs){
                  $langs = array_unique(array_merge(...$langs));
               }
               sort($langs);
               $course->lang = $langs;

               $course->save();
            });
            break;
      }
   }

   /*
    * Update App\Models\Course topics_count
    */
   public function updateTotalTopics(int $id = null): void
   {
      $id = $id ?? null;
      switch ($id) {
         case true:
            $topic = Topic::with(['course'])->find($id);
            if($topic->course) {
               $course = $topic->course;
               $course->topics_count--;
//                    $course->lessons_count = 0;
//                    $course->total_lessons_duration = 0;
               $course->save();
            }
            break;

         case false:
            // for courses
            $courses = Course::with('topics')->get();
            /* one course sum all related topics */
            $courses->each(function($course) {
               if($course->topics) {
                  $course->topics_count = $course->topics->count();
                  $course->save();
               }
            });
            break;
      }
   }

   /*
    * Update App\Models\Topic total_lessons_duration
    * Update App\Models\Course total_lessons_duration
    */
   public function updateTotalDuration(int $id = null, $isRemoving = false): void
   {
      if ($isRemoving === true && is_integer($id)) {
         $lesson = Lesson::with(['topic'])->find($id);
         if($lesson->topic) {
            $topic = $lesson->topic;
            $topic->total_lessons_duration = $topic->total_lessons_duration - $lesson->video_duration;
            $topic->save();

            if($lesson->topic->course) {
               $course = $lesson->topic->course;
               $course->total_lessons_duration = $course->total_lessons_duration - $lesson->video_duration;
               $course->save();
            }
         }
      } else {
         // for topics
         $topics = Topic::get();
         foreach ($topics as $topic) {
            $topic->total_lessons_duration = $this->getTopicTotalDuration($topic);
            $topic->save();
         }
         // for courses
         $courses = Course::get();
         foreach ($courses as $course) {
            $course->total_lessons_duration = $this->getCourseTotalDuration($course);
            $course->save();
         }
      }
   }

   public function getTopicTotalDuration(Topic $topic): int
   {
      $totalDuration = 0;
      if ($topic) {
         foreach ($topic->lessons as $lesson) {
            $totalDuration += $lesson->video_duration;
         }
      }
      return $totalDuration;
   }

   public function getCourseTotalDuration(Course $course): int
   {
      $totalDuration = 0;
      if ($course) {
         foreach ($course->lessons as $lesson) {
            $totalDuration += $lesson->video_duration;
         }
      }
      return $totalDuration;
   }

   public function updateDateSeats(int $id = null): bool
   {
      $date = Date::find($id);
      if ($date) {
         $tickets = Ticket::where('date_id', $date->id)->get();
         $booked = 0;
         $purchased = 0;
         foreach ($tickets as $ticket) {
            if ($ticket->is_purchased === 1) {
               $purchased += (int) ($ticket->count ?? 1);
            }
            if ($ticket->is_canceled !== 1 && $ticket->is_purchased !== 1) {
               $booked += (int) ($ticket->count ?? 1);
            }
         }
         $date->seats_booked = $booked;
         $date->seats_purchased = $purchased;
         $date->seats_vacant = $date->seats_total - ($booked + $purchased);
         $date->save();
         return true;
      }
      return false;
   }
}
