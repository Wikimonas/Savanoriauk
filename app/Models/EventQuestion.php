<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventQuestion extends Model
{
    use HasFactory;

    protected $fillable = ['event_id', 'question'];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}

