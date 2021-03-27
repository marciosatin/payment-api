<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidTransactionException;
use App\Services\TransactionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Validator;

class TransactionController extends Controller
{

    /**
     * @var TransactionService
     */
    protected $transaction;

    /**
     * Create a new controller instance.
     *
     * @param TransactionService $transaction
     * @return void
     */
    public function __construct(TransactionService $transaction)
    {
        $this->transaction = $transaction;
    }

    /**
     * Create transaction.
     *
     * @param Request $request
     * @return array
     */
    public function create(Request $request): JsonResponse
    {

        try {
            $validator = Validator::make($request->all(), [
                        'payer_id' => 'required|exists:users,id',
                        'payee_id' => 'required|exists:users,id',
                        'value' => 'required|gt:0'
            ]);

            if ($validator->fails()) {
                return $this->responseErrorClient($validator->errors()->toArray());
            }

            return response()->json($this->transaction->create($request->all()));
        } catch (InvalidTransactionException $exc) {
            return $this->responseErrorClient([
                        'transaction' => [$exc->getMessage()]
            ]);
        } catch (\Exception $exc) {
            return $this->responseErrorServer([
                        'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                        'message' => 'Unknown server error'
            ]);
        }
    }

}