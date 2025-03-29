<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;

class Event extends Model
{

    /** @use HasFactory<EventFactory> */
    use HasFactory, Notifiable;
    protected $fillable = [
        'name',
        'description',
        'address',
        'event_date',
        'organiser_id',
    ];

    public function organiser (): BelongsTo
    {
        return $this->belongsTo(User::class, 'organiser_id');
    }
}
