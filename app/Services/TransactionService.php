<?php

namespace App\Services;

use App\Exceptions\InvalidTransactionException;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserType;
use Exception;
use Illuminate\Support\Facades\DB;

class TransactionService
{

    public function create(array $data)
    {
        
        try {
            DB::beginTransaction();

            $value = floatval($data['value']);
            if ($value <= 0) {
                throw new InvalidTransactionException('The value must be greater than 0');
            }

            $payer = User::find($data['payer_id']);
            $payee = User::find($data['payee_id']);

            if ($payer->type->type_name == UserType::TYPE_LOJISTA) {
                throw new InvalidTransactionException('Transaction not allowed for that user');
            }

            if ($payer->id == $payee->id) {
                throw new InvalidTransactionException('Invalid transaction for payer and payee');
            }

            if ($payer->balance < $value) {
                throw new InvalidTransactionException('Insufficient funds for payer');
            }

            $transaction = Transaction::create([
                'payer_id' => $payer->id,
                'payee_id' => $payee->id,
                'value' => $value
            ]);

            $payer->balance -= $value;
            $payer->save();

            $payee->balance += $value;
            $payee->save();
            
            if (!NotificationService::send($payee)) {
                //add in queue
            }

            DB::commit();
        } catch (Exception $exc) {
            DB::rollBack();
            throw $exc;
        }

        return $transaction->toArray();
    }

}