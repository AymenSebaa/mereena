<?php

namespace App\Mail;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TaskMail extends Mailable {
    use Queueable, SerializesModels;

    public $task;
    public $recipientRole;

    /**
     * Create a new message instance.
     */
    public function __construct(Task $task, int $recipientRole) {
        $this->task = $task;
        $this->recipientRole = $recipientRole;
    }

    /**
     * Build the message.
     */
    public function build() {
        return $this->subject("New Departure Notification")
            ->view('emails.task')
            ->with([
                'task' => $this->task,
                'recipientRole' => $this->recipientRole,
            ]);
    }
}
