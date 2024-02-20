<?php

namespace App\Http\Controllers;

use App\Exceptions\TransactionDeniedException;
use App\Http\Requests\StoreTransactionRequest;
use App\Services\Transaction\TransactionService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class TransactionController extends Controller
{

    public function __construct(private TransactionService $transactionService)
    {
    }

    /**
     * Show the form for creating a new resource.
     */
    public function transfer(StoreTransactionRequest $request)
    {
        try {
            $result = $this->transactionService->handleTransaction($request->validated());
            return response()->json([
                'message' => 'Your transaction was concluded, we have sent an email to the payee.',
                'data' => $result
            ]);
        } catch (TransactionDeniedException $exception) {
            return response()->json(['errors' => ['message' => $exception->getMessage()]], $exception->getCode());
        } catch (QueryException $queryException) {
            return response()->json(['errors' => ['message' => $queryException->getMessage()]], $queryException->getCode());
        } catch (ModelNotFoundException $modelNotFoundException) {
            return response()->json(['errors' => ['message' => $modelNotFoundException->getMessage()]], $modelNotFoundException->getCode());
        } catch (\Exception $exception) {
            return response()->json(['errors' => ['message' =>  $exception->getMessage()]], 500);
        }
    }
}
