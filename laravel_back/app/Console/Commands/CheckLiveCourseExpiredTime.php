<?php

namespace App\Console\Commands;

use App\Date;
use App\Jobs\SendCmnEmail;
use App\Mail\CmnMail;
use App\Ticket;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CheckLiveCourseExpiredTime extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'live-course:check_time';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check and set expired live-course field is_expired to true';

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
        $dates = Date::get();
        foreach ($dates as $date) {
            $diff = Carbon::now()->diffInDays(Carbon::parse($date->start), false);
//            array_push($times, $diff);
            if ($diff <= 0) {
                $date->is_expired = true;
                $date->save();
            }
        }
        $tickets = Ticket::where([
           ['is_expired', '!=', '1'],
           ['is_canceled', '!=', '1']
        ])->get();
        foreach ($tickets as $ticket) {
            if ($ticket->date['is_expired'] == 1) {
                $ticket->is_expired = true;
                $ticket->save();
            }
            if ($ticket->is_purchased == 1 && $ticket->is_reminded != 1) {
                $diff = Carbon::now()->diffInDays(Carbon::parse($ticket->date->start), false);
                array_push($times, $diff);
                if ($diff == 1) {
                    $user = $ticket->user;
                    $date = $ticket->date;
                    $date['event'] = $date->event;
                    Log::debug('LIVE COURSE TOMORROW REMINDER');
                    SendCmnEmail::dispatch($user->email, 'Booking', 'email.live_course_tomorrow_reminder', (object) $date, $user);
                    $ticket->is_reminded = true;
                    $ticket->save();
                }
            }
        }
        Log::debug('LIVE COURSE CHECK EXPIRED');
        Log::debug(json_encode($times));
    }
}
