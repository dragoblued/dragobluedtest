<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Setting;
class ApiPasswordResetSuccess extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    protected $data;
    public function __construct()
    {
        $social_links_url = Setting::where('key','social_links')->first();
        if ($social_links_url) {
            $social_links_url = json_decode($social_links_url->value);
            $social_urls = [];
            foreach ($social_links_url as $key => $value) {
                 $social_urls[$value->name]  = ['url' => $value->url];
            }
            $this->data['settings']['social_urls'] = $social_urls;
        }

        $email_newsletter_text = Setting::where('key','email_newsletter')->first()->value;
        $email = Setting::where('key','email')->first()->value;

        $this->data['settings']['email_newsletter'] = $email_newsletter_text;
        $this->data['settings']['email'] = $email;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {


        return (new MailMessage)
           ->subject('Password Changed')
           ->view(
            'email.success_verify', [
                'settings' => $this->data['settings']
            ]
        );
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
