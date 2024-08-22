<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AccountResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'numero_conta' => $this->numero_conta,
            'saldo' => $this->saldo,
        ];
    }
}
