<?php

namespace App\Http\Controllers;

use App\Services\TransactionService;
use Illuminate\Http\Request;
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
    public function create(Request $request): array
    {

        $validator = Validator::make($request->all(), [
            'payer_id' => 'required|exists:users,id',
            'payee_id' => 'required|exists:users,id',
            'value' => 'required|gt:0'
        ]);

        if ($validator->fails()) {
            return $validator->errors()->toArray();
        }


        return $this->transaction->create($request->all());
    }

}