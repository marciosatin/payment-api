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

        $validator = Validator::make($request->all(), [
                    'payer_id' => 'required|exists:users,id',
                    'payee_id' => 'required|exists:users,id',
                    'value' => 'required|gt:0'
        ]);

        if ($validator->fails()) {
            return $this->responseErrorClient($validator->errors()->toArray());
        }

        try {
            return response()->json($this->transaction->create($request->all()));
        } catch (InvalidTransactionException $exc) {
            return $this->responseErrorClient([
                        'code' => Response::HTTP_BAD_REQUEST,
                        'message' => $exc->getMessage()
            ]);
        } catch (\Exception $exc) {
            return $this->responseErrorServer([
                        'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                        'message' => 'Unknown server error'
            ]);
        }
    }

}