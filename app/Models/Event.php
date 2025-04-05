<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use League\Uri\Http;

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

    public function questions(): HasMany {
        return $this->hasMany(EventQuestion::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(EventApplication::class);
    }

    public function applicationForUser($userId)
    {
        return $this->applications()->where('user_id', $userId)->first();
    }
}
