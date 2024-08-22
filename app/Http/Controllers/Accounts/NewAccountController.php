<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use App\Http\Requests\NewAccountRequest;
use App\Services\Accounts\Contracts\AccountServiceContract;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class NewAccountController extends Controller
{
    public function __construct(private AccountServiceContract $accountService)
    {
    }

    public function __invoke(NewAccountRequest $request)
    {
        $data = $request->validated();

        try {
            $account = $this->accountService->create($data);
        } catch (Exception $exception) {
            Log::error($exception->getMessage(), [
                'data' => $data,
                'exception' => $exception,
                'code' => 'account_flow_error_exception_not_expected'
            ]);

            return response()->json(
                ['message' => $exception->getMessage()],
                Response::HTTP_NOT_FOUND
            );
        }

        return response()->json($account, Response::HTTP_CREATED);
    }
}
