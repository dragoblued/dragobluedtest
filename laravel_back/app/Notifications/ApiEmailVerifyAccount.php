<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Setting;
use Illuminate\Support\Facades\Config;

class ApiEmailVerifyAccount extends Notification implements ShouldQueue
{
   use Queueable;

   /**
    * Create a new notification instance.
    *
    * @return void
    */
   protected $data;
   protected $isRecovery;

   public function __construct($isRecovery = false)
   {
      $this->isRecovery = $isRecovery;

      $social_links_url = Setting::where('key','social_links')->first();
      if ($social_links_url) {
         $social_links_url = json_decode($social_links_url->value);
         $social_urls = [];
         foreach ($social_links_url as $key => $value) {
            $social_urls[$value->name]  = ['url' => $value->url];
         }
         $this->data['settings']['social_urls'] = $social_urls;
      }

      $email_newsletter = Setting::where('key','email_newsletter')->first()->value;
      $email = Setting::where('key','email')->first()->value;

      $this->data['settings']['email_newsletter'] = $email_newsletter;
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
    * @return MailMessage
    */
   public function toMail($notifiable)
   {
      $url = url(config('app.site_url').'/auth/verify/'.$notifiable->activation_token);
      return (new MailMessage)
         ->subject('Email Verification')
         ->view(
         'email.verify', [
            'url' => $url,
            'settings'=>$this->data['settings'],
            'isRecovery' => $this->isRecovery
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
