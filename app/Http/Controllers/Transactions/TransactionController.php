<?php

namespace App\Http\Controllers\Transactions;

use App\Exceptions\AccountNotFoundException;
use App\Exceptions\InsufficientBalanceException;
use App\Http\Controllers\Controller;
use App\Http\Requests\TransactionRequest;
use App\Services\Transactions\Contracts\TransactionServiceContract;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class TransactionController extends Controller
{
    public function __construct(
        private TransactionServiceContract $transactionService
    ) {
    }

    public function __invoke(TransactionRequest $request)
    {
        $data = $request->validated();

        try {
            $accountStatus = $this->transactionService->handleTransaction($data);

            return response()->json($accountStatus, Response::HTTP_CREATED);
        } catch (AccountNotFoundException $accountNotFound) {
            Log::info($accountNotFound->getMessage(), [
                'data' => $data,
                'exception' => $accountNotFound,
                'code' => 'transaction_error_account_not_found'
            ]);

            return response()->json(
                [
                    'message' => $accountNotFound->getMessage()
                ],
                Response::HTTP_NOT_FOUND
            );
        } catch (InsufficientBalanceException $insufficientBalance) {
            Log::info($insufficientBalance->getMessage(), [
                'data' => $data,
                'exception' => $insufficientBalance,
                'code' => 'transaction_error_no_balance'
            ]);

            return response()->json(
                [
                    'message' => $insufficientBalance->getMessage()
                ],
                Response::HTTP_NOT_FOUND
            );
        } catch (Exception $exception) {
            Log::critical($exception->getMessage(), [
                'data' => $data,
                'exception' => $exception,
                'code' => 'transaction_error_critical_exception'
            ]);

            abort(Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
