<?php

namespace App\Mail;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReservationMail extends Mailable {
    use Queueable, SerializesModels;

    public $reservation;
    public $recipientRole;
    public $event; // created | approved | rejected

    public function __construct(Reservation $reservation, int $recipientRole, string $event) {
        $this->reservation    = $reservation;
        $this->recipientRole  = $recipientRole;
        $this->event          = $event;
    }

    public function build() {
        $subject = "Reservation #{$this->reservation->id} - {$this->reservation->type->name}";

        return $this->subject($subject)
            ->view('emails.reservation');
    }
}
