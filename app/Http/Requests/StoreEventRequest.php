<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEventRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name'        => 'required|string|min:3|max:255',
            'description' => 'required|string|min:3',
            'address'     => 'required|string|min:3',
            'event_date'  => 'required|date|after:today',
        ];
    }
}
