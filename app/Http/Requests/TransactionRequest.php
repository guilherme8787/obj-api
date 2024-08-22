<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransactionRequest extends FormRequest
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
            'forma_pagamento' => 'required|string|in:P,C,D',
            'numero_conta' => 'required|integer|exists:contas,numero_conta',
            'valor' => 'required|numeric|min:0.01',
        ];
    }

    /**
     * @return array
     */
    public function messages()
    {
        return [
            'forma_pagamento.required' => 'A forma de pagamento é obrigatória.',
            'forma_pagamento.in' => 'A forma de pagamento deve ser P (Pix), C (Cartão de Crédito) ou D (Cartão de Débito).',
            'numero_conta.required' => 'O número da conta é obrigatório.',
            'numero_conta.exists' => 'O número da conta informado não existe.',
            'valor.required' => 'O valor é obrigatório.',
            'valor.numeric' => 'O valor deve ser numérico.',
            'valor.min' => 'O valor mínimo para transações é 0.01.',
        ];
    }
}
