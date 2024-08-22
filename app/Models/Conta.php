<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conta extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $table = 'contas';

    /**
     * @var array
     */
    protected $fillable = [
        'numero_conta',
        'saldo',
    ];

    /**
     * @var HasMany
     */
    public function transacoes()
    {
        return $this->hasMany(Transacao::class, 'numero_conta', 'numero_conta');
    }
}
