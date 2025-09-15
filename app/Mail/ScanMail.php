<?php

namespace App\Mail;

use App\Models\Scan;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ScanMail extends Mailable {
    use Queueable, SerializesModels;

    public $scan;
    public $recipientRole;

    /**
     * Create a new message instance.
     */
    public function __construct(Scan $scan, int $recipientRole) {
        $this->scan = $scan;
        $this->recipientRole = $recipientRole;
    }

    /**
     * Build the message.
     */
    public function build() {
        return $this->subject("New Scan Notification")
            ->view('emails.scan')
            ->with([
                'scan' => $this->scan,
                'recipientRole' => $this->recipientRole,
            ]);
    }
}
