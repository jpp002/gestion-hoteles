<?php

namespace App\Http\Requests\Hotel;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class PutRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $hotelId = $this->route('hotel');

        return [
            'nombre' => 'required|min:5|max:50',
            'direccion' => 'required|min:5|max:100|unique:hoteles,direccion,' . $hotelId,
            'telefono' => 'required|max:20|unique:hoteles,telefono,' . $hotelId,
            'email' => 'required|min:5|unique:hoteles,email,' . $hotelId,
            'sitioWeb' => 'required|min:5|unique:hoteles,sitioWeb,' . $hotelId,
        ];
    }



    public function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        if ($this->expectsJson()) {
            $response = new Response($validator->errors(), 400);
            throw new ValidationException($validator, $response);
        }
    }
}
