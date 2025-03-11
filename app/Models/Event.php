<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Event extends Model
{
    protected $fillable = [
        'name',
        'description',
        'event_date',
        'organiser_id',
    ];

    public function organiser (): BelongsTo
    {
        return $this->belongsTo(User::class, 'organiser_id');
    }
}
