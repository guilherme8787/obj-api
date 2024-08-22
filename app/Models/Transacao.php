<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transacao extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $table = 'transacoes';

    /**
     * @var array
     */
    protected $fillable = [
        'numero_conta',
        'forma_pagamento',
        'valor',
        'saldo_apos_transacao',
    ];

    /**
     * @return BelongsTo
     */
    public function conta()
    {
        return $this->belongsTo(Conta::class, 'numero_conta', 'numero_conta');
    }
}
