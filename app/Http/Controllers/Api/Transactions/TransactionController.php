<?php

namespace App\Http\Controllers\Api\Transactions;

use App\Http\Controllers\Controller;
use App\Http\Requests\TransactionRequest;
use App\Services\Transactions\Contracts\TransactionServiceContract;
use Illuminate\Http\Response;

class TransactionController extends Controller
{
    public function __construct(
        private TransactionServiceContract $transactionService
    ) {
    }

    public function __invoke(TransactionRequest $request)
    {
        $data = $request->validated();

        $this->transactionService->handleTransaction($data);

        return response()->json(
            [
                'message' => 'Transação realizada com sucesso.'
            ],
            Response::HTTP_CREATED
        );
    }
}
