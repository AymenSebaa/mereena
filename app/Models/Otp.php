<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Otp extends Model {
    use HasFactory;

    protected $table = 'otps';

    protected $fillable = [
        'email',
        'code',
        'expires_at',
        'used_at',
        'attempts',
        'next_attempt_at'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
        'next_attempt_at' => 'datetime',
    ];

    public function markAsUsed() {
        $this->used_at = now();
        $this->save();
    }

    public function canAttempt(): bool {
        return !$this->next_attempt_at || $this->next_attempt_at->isPast();
    }

    public function incrementAttempt(int $baseCooldownSeconds = 60) {
        $this->attempts++;
        $this->next_attempt_at = now()->addSeconds($baseCooldownSeconds);
        $this->save();
    }

    public function resetAttempts() {
        $this->attempts = 0;
        $this->next_attempt_at = null;
        $this->save();
    }

    public function isValid(): bool {
        return !$this->used_at && now()->lte($this->expires_at);
    }
}
