<?php
// app/Models/Task.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model {
    use HasFactory;

    protected $fillable = [
        'external_id',
        'site_id',
        'device_id',
        'user_id',
        'title',
        'comment',
        'priority',
        'status',
        'invoice_number',
        'pickup_address',
        'pickup_address_lat',
        'pickup_address_lng',
        'pickup_time_from',
        'pickup_time_to',
        'delivery_address',
        'delivery_address_lat',
        'delivery_address_lng',
        'delivery_time_from',
        'delivery_time_to',
        'distance',
        'duration',
        'polyline',
        'directions',
    ];

    protected $casts = [
        'directions' => 'array',
    ];

    public function site() {
        return $this->belongsTo(Hotel::class, 'site_id', 'id');
    }

    public function bus() {
        return $this->belongsTo(Bus::class, 'device_id', 'external_id');
    }
}
