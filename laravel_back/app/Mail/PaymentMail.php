<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use PDF;
class PaymentMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    private $data;

    public function __construct(array $request)
    {
        $this->data = $request;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $customPaper = array(0,0,260,260);
        $pdf = PDF::loadView('site.payment.cheque', $this->data)->setPaper($customPaper);

        $view = 'email.cheque';
        $subject = 'Payment';

        return $this->subject($subject)
            ->view($view)
            ->attachData($pdf->output(),'customer.pdf')
            ->with('data',$this->data);
    }
}
