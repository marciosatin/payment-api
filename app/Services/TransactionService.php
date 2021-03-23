<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;

class TransactionService
{

    public function create(array $data)
    {

        try {
            DB::beginTransaction();
            
            $payer = User::find($data['payer_id']);
            $payee = User::find($data['payee_id']);
            
            if ($payer->type->type_name == 'lojista') {
                throw new Exception('Transaction not allowed for that user');
            }
            
            if ($payer->id == $payee->id) {
                throw new Exception('Invalid transaction for payer and payee');
            }
            
            if ($payer->balance < floatval($data['value'])) {
                throw new Exception('Insufficient funds for payer');
            }

            $transaction = Transaction::create($data);

            $payer->balance -= $data['value'];
            $payer->save();

            $payee->balance += $data['value'];
            $payee->save();

            DB::commit();
        } catch (Exception $exc) {
            DB::rollBack();
            return [
                'message' => $exc->getMessage()
            ];
        }

        return $transaction->toArray();
    }

}