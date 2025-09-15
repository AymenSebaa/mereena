<?php

namespace App\Mail;

use App\Models\Complaint;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ComplaintMail extends Mailable {
    use Queueable, SerializesModels;

    public $complaint;
    public $recipientRole;
    public $event; // created | status_updated

    public function __construct(Complaint $complaint, int $recipientRole, string $event) {
        $this->complaint     = $complaint;
        $this->recipientRole = $recipientRole;
        $this->event         = $event;
    }

    public function build() {
        $subject = "Complaint #{$this->complaint->id} – {$this->complaint->subject}";

        return $this->subject($subject)
            ->view('emails.complaint');
    }
}
