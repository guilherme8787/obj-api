<?php

namespace App\Http\Controllers\Accounts;

use App\Exceptions\AccountNotFoundException;
use App\Http\Controllers\Controller;
use App\Services\Accounts\Contracts\AccountServiceContract;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class ShowAccountController extends Controller
{
    public function __construct(private AccountServiceContract $accountService)
    {
    }

    public function __invoke(Request $request)
    {
        $data = $request->validated();

        try {
            $account = $this->accountService->get($data);
        } catch (AccountNotFoundException $accountNotFound) {
            Log::info($accountNotFound->getMessage(), [
                'data' => $data,
                'exception' => $accountNotFound,
                'code' => 'account_flow_error_account_not_found'
            ]);

            return response()->json(
                [
                    'message' => $accountNotFound->getMessage()
                ],
                Response::HTTP_NOT_FOUND
            );
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

        return response()->json($account, Response::HTTP_OK);
    }
}
