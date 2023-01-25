<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Setting;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class CmnMail extends Mailable
{
   use Queueable, SerializesModels;

   /**
    * The feedback instance.
    *
    * @var CmnMail
    */
   private $data;
//   private $fromName;

   /**
    * Create a new message instance.
    *
    * @return void
    */
   public function __construct($data)
   {
      $social_links_url = Setting::where('key','social_links')->first();
      if ($social_links_url) {
         $social_links_url = json_decode($social_links_url->value);
         $social_urls = [];
         foreach ($social_links_url as $key => $value) {
            if ($value->name === 'instagram' || $value->name === 'facebook' || $value->name === 'youtube') {
               $social_urls[$value->name]  = ['url' => $value->url];
            }
         }
         $this->data['settings']['social_urls'] = $social_urls;
      }

      $email_newsletter_text = Setting::where('key','email_newsletter')->first();
//      $this->fromName = Setting::where('key','email_title')->first()->value ?? config('mail.from.name');
      $email = Setting::where('key', 'email')->first();
      $currencyRaw = json_decode(Setting::where('key', 'currency')->first()->value);
      $currencySign = '€';
      if (is_array($currencyRaw)) {
         foreach ($currencyRaw as $currencyItem) {
            if ($currencyItem->selected == true) {
               $currencySign = $currencyItem->sign;
               break;
            }
         }
      }
      $this->data['settings']['currency_sign'] = $currencySign;

      if ($email_newsletter_text && $email) {
         $this->data['settings']['email_newsletter'] = $email_newsletter_text->value;
         $this->data['settings']['email'] = $email->value;
      }
      $this->data['settings'] = (object) $this->data['settings'];

      $this->data['subject'] = $data->subject ?? 'Feedback from Algirdas Puisys site';
      $this->data['view'] = $data->view ?? 'email.feedback';
      $this->data['content'] = $data->content ?? null;
      $this->data['user'] = $data->user ?? null;
      $this->data['purchaseUser'] = $data->purchaseUser ?? null;
      $this->data['attachmentPath'] = $data->attachmentPath ?? null;
      $this->data['attachmentName'] = $data->attachmentName ?? null;
      $this->data['attachmentMime'] = $data->attachmentMime ?? null;
   }

   /**
    * Build the message.
    *
    * @return $this
    */
   public function build()
   {
      if (File::exists($this->data['attachmentPath'])) {
         return $this->subject($this->data['subject'])
            ->view($this->data['view'])
            ->attach($this->data['attachmentPath'], [
               'as' => $this->data['attachmentName'],
               'mime' => $this->data['attachmentMime'],
            ])
            ->with([
               'data' => $this->data['content'],
               'user' => $this->data['user'],
               'purchaseUser' => $this->data['purchaseUser'],
               'settings' => $this->data['settings']
            ]);
      }
      return $this->subject($this->data['subject'])
         ->view($this->data['view'])
         ->with([
            'data' => $this->data['content'],
            'user' => $this->data['user'],
            'purchaseUser' => $this->data['purchaseUser'],
            'settings' => $this->data['settings']
         ]);
   }
}
