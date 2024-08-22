<?php

namespace App\Repositories\Transacao;

use App\Models\Transacao;

class TransacaoRepository implements TransacaoRepositoryContract
{
    private Transacao $transaction;

    public function __construct(Transacao $transaction)
    {
        $this->transaction = $transaction;
    }

    public function create(array $data): Transacao
    {
        return $this->transaction->create($data);
    }
}
