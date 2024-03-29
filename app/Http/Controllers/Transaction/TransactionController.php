<?php

namespace App\Http\Controllers\Transaction;

use App\Exceptions\TransactionDeniedException;
use App\Exceptions\TransactionUnauthorizedException;
use App\Http\Controllers\Controller;
use App\Http\DTO\Transaction\TransactionOutputDTO;
use App\Http\Requests\StoreTransactionRequest;
use App\Services\Transaction\TransactionService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;

class TransactionController extends Controller
{
    public function __construct(private TransactionService $transactionService)
    {
    }

    public function transfer(StoreTransactionRequest $request): JsonResponse
    {
        try {
            $result = $this->transactionService->handleTransaction($request->validated());

            $outputDTO = TransactionOutputDTO::fromArray($result->toArray());
            
            return response()->json($outputDTO->response());
        } catch (TransactionDeniedException $exception) {
            return response()->json(['errors' => ['message' => $exception->getMessage()]], $exception->getCode());
        } catch (QueryException $queryException) {
            return response()->json(['errors' => ['message' => $queryException->getMessage()]], $queryException->getCode());
        } catch (ModelNotFoundException $notFoundException) {
            return response()->json(['errors' => ['message' => $notFoundException->getMessage()]], $notFoundException->getCode());
        } catch (TransactionUnauthorizedException $authException) {
            return response()->json(['errors' => ['message' => $authException->getMessage()]], $authException->getCode());
        } catch (\Exception $exception) {
            return response()->json(['errors' => ['message' =>  $exception->getMessage()]], 500);
        }
    }
}
