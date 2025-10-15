<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStockRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'new_stock' => 'required|integer',
            'operation' => 'required|in:add,subtract',
            'source' => 'nullable|string|max:100',
            'note' => 'nullable|string|max:512',
        ];
    }

    public function messages(): array
    {
        return [
            'new_stock.required' => 'La cantidad nueva de stock es obligatoria.',
            'new_stock.integer' => 'La cantidad debe ser un número entero.',
            'operation.required' => 'La operación es obligatoria.',
            'operation.in' => 'La operación debe ser "add" o "subtract".',
            'source.string' => 'La fuente debe ser un texto válido.',
            'note.string' => 'La nota debe ser un texto válido.',
        ];
    }

    /**
     * Forzar que las validaciones devuelvan JSON (422) en lugar de HTML.
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'message' => 'Errores de validación',
                'errors' => $validator->errors(),
            ], 422)
        );
    }
}
