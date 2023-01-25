<?php

namespace App\Jobs;

use App\Mail\CmnMail;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendCmnEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;

   /**
    * Create a new job instance.
    *
    * @param $recipient
    * @param $subject
    * @param $view
    * @param null $content
    * @param null $user
    * @param null $purchaseUser
    * @param array $attachment
    */
    public function __construct($recipient, $subject, $view, $content = null, $user = null, $purchaseUser = null, array $attachment = [])
    {
        $this->data['recipient'] = $recipient;
        $this->data['subject'] = $subject;
        $this->data['view'] = $view;
        $this->data['content'] = $content;
        $this->data['user'] = $user;
        $this->data['purchaseUser'] = $purchaseUser;
        $this->data['attachmentPath'] = $attachment['attachmentPath'] ?? null;
        $this->data['attachmentName'] = $attachment['attachmentName'] ?? null;
        $this->data['attachmentMime'] = $attachment['attachmentMime'] ?? null;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        Mail::to($this->data['recipient'])
            ->send(new CmnMail((object) [
                'subject' => $this->data['subject'],
                'view' => $this->data['view'],
                'content' => $this->data['content'],
                'user' => $this->data['user'],
                'purchaseUser' => $this->data['purchaseUser'],
                'attachmentPath' => $this->data['attachmentPath'],
                'attachmentName' => $this->data['attachmentName'],
                'attachmentMime' => $this->data['attachmentMime']
            ]));
    }
}
