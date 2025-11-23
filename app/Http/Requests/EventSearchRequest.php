<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EventSearchRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'query' => 'required|string|min:3'
        ];
    }
}
