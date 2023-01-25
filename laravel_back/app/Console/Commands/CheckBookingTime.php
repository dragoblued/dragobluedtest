<?php

namespace App\Console\Commands;

use App\Classes\UpdateTotalCount;
use App\Jobs\SendCmnEmail;
use App\Mail\CmnMail;
use App\Ticket;
use App\Traits\StatsCounter;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CheckBookingTime extends Command
{
   use StatsCounter;

   /**
    * The name and signature of the console command.
    *
    * @var string
    */
   protected $signature = 'booking:check_time';

   /**
    * The console command description.
    *
    * @var string
    */
   protected $description = 'Check if booking time is expired';

   /**
    * Create a new command instance.
    *
    * @return void
    */
   public function __construct()
   {
      parent::__construct();
   }

   /**
    * Execute the console command.
    *
    * @return mixed
    */
   public function handle()
   {
      $times = [];
      $tickets = Ticket::where([
         ['is_expired', '!=', '1'],
         ['is_purchased', '!=', '1'],
         ['is_canceled', '!=', '1']
      ])->get();
      foreach ($tickets as $ticket) {
         $diff = Carbon::now()->diffInMinutes(Carbon::parse($ticket->created_at));
         $date = $ticket->date;
         $date['event'] = $date->event;
         array_push($times, $diff);
         Log::debug($date['start']);
         Log::debug($diff);
         Log::debug($diff >= 60);
         if ($diff >= 60) {
            $user = $ticket->user;
            Log::debug('BOOKING CLOSED LETTER');
            Log::debug('USER');
            Log::debug($user['email']);
            SendCmnEmail::dispatch($user->email, 'Booking', 'email.booking_closed', (object) $date, $user);
            /* Увеличиваем счетчик вакантных мест для live-курса(event, date) */
//            $this->incrementCount(
//               'Date',
//               $date->id,
//               ['id'],
//               'seats_vacant',
//               $ticket->count
//            );

            $ticket->is_canceled = true;
            $ticket->save();
            /* Recalculate booked and purchased seats number for live-course(event) */
            (new UpdateTotalCount())->updateDateSeats($date['id']);

            continue;
         }
         if ($diff >= 30 && $diff < 60) {
            if ($ticket->is_reminded != 1) {
               $user = $ticket->user;
               Log::debug('BOOKING REMINDER LETTER');
               Log::debug('USER');
               Log::debug($user['email']);
               SendCmnEmail::dispatch($user->email, 'Booking', 'email.booking_reminder', (object) $date, $user);

               $ticket->is_reminded = true;
               $ticket->save();
            }
         }
      }
      Log::debug('BOOKING TIME CHECKED');
      Log::debug(json_encode($times));
   }
}
