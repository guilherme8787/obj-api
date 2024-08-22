<?php

namespace App\Repositories\Transacao;

use App\Models\Transacao;

interface TransacaoRepositoryContract
{
    public function create(array $data): ?Transacao;
}
