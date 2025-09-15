<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendOtpMail extends Mailable {
    use Queueable, SerializesModels;

    public $otp;
    public $user;

    /**
     * Create a new message instance.
     */
    public function __construct($user, $otp) {
        $this->user = $user;
        $this->otp = $otp;
    }

    /**
     * Build the message.
     */
    public function build() {
        return $this->subject('Your OTP Code')
            ->view('emails.otp');
    }
}
