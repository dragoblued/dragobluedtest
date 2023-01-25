<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Setting;
class FeedbackMail extends Mailable
{
	use Queueable, SerializesModels;

	/**
	 * The feedback instance.
	 *
	 * @var Feedback
	 */
	private $data;

	/**
	 * Create a new message instance.
	 *
	 * @return void
	 */
	public function __construct($data)
	{
		$this->data = $data;
		$social_links_url = Setting::where('key','social_links')->first();
        if ($social_links_url) {
            $social_links_url = json_decode($social_links_url->value);
            $social_urls = [];
            foreach ($social_links_url as $key => $value) {
                if ($value->name === 'instagram' || $value->name === 'facebook' || $value->name === 'youtube') {
                    $social_urls[$value->name]  = ['url' => $value->url];
                }   
            }
            $this->data['social_urls'] = $social_urls;
        }

        $email_newsletter_text = Setting::where('key','email_newsletter')->first();
        $email = Setting::where('key','email')->first();

        if ($email_newsletter_text && $email) {
            $this->data['email_newsletter'] =   $email_newsletter_text->value;
            $this->data['email'] =   $email->value;
        }
	}

	/**
	 * Build the message.
	 *
	 * @return $this
	 */
	public function build()
	{
		return $this->subject('Feedback from Algirdas Puisys site')
		->view('email.feedback')
		->with($this->data);
	}
}
