<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FlightUpdateMail extends Mailable {
    use Queueable, SerializesModels;

    public function build() {
        return $this->subject("✈️ Exciting New Features in the TFA App - Track Flights in Real Time!")
            // ->view('emails.flights_update');
            ->view('emails.book_flights');
    }
}
