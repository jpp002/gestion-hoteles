<?php

namespace App\Http\Requests\Hotel;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class StoreCascadaRequest extends FormRequest
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
        return [
            'nombre' => 'required|min:5|max:50',
            'direccion' => 'required|min:5|max:100|unique:hoteles',
            'telefono' => 'required|max:20|unique:hoteles',
            'email' => 'required|email|unique:hoteles',
            'sitioWeb' => 'required|url|unique:hoteles',
            'habitaciones' => 'array',
            'habitaciones.*.numero' => 'required|string|max:20',
            'habitaciones.*.tipo' => 'required|string|max:50',
            'habitaciones.*.precioNoche' => 'required|numeric|min:0',
            'servicios' => 'array',
            'servicios.*.nombre' => 'required|string|max:100',
            'servicios.*.descripcion' => 'nullable|string|max:255',
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
