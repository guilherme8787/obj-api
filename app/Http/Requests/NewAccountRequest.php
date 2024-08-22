<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NewAccountRequest extends FormRequest
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
            'numero_conta' => 'required|integer|unique:contas,numero_conta',
            'saldo' => 'required|numeric|min:0',
        ];
    }

    public function messages()
    {
        return [
            'numero_conta.required' => 'O número da conta é obrigatório.',
            'numero_conta.integer' => 'O número da conta deve ser um inteiro.',
            'numero_conta.unique' => 'Esse número de conta já existe.',
            'saldo.required' => 'O saldo é obrigatório.',
            'saldo.numeric' => 'O saldo deve ser um número.',
            'saldo.min' => 'O saldo deve ser maior ou igual a zero.',
        ];
    }
}
