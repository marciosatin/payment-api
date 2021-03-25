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

            $this->validateValueGreaterThanZero($value);

            $payer = User::find($data['payer_id']);
            $payee = User::find($data['payee_id']);

            $this->validateTransactionNotAllowedForUser($payer);
            $this->validatePayerEqualsPayee($payer, $payee);
            $this->validateInsufficientFoundsForPayer($payer, $value);

            $transaction = Transaction::create([
                        'payer_id' => $payer->id,
                        'payee_id' => $payee->id,
                        'value' => $value
            ]);

            $payer->balance -= $value;
            $payer->save();

            $payee->balance += $value;
            $payee->save();

            DB::commit();

            NotificationService::send($payee);
        } catch (Exception $exc) {
            DB::rollBack();
            throw $exc;
        }

        return $transaction->toArray();
    }

    private function validateValueGreaterThanZero(float $value)
    {
        if ($value <= 0) {
            throw new InvalidTransactionException('The value must be greater than 0');
        }
    }

    private function validateTransactionNotAllowedForUser(User $user)
    {
        if ($user->type->type_name == UserType::TYPE_LOJISTA) {
            throw new InvalidTransactionException('Transaction not allowed for that user');
        }
    }

    private function validatePayerEqualsPayee($payer, $payee)
    {
        if ($payer->id == $payee->id) {
            throw new InvalidTransactionException('Invalid transaction for payer and payee');
        }
    }

    private function validateInsufficientFoundsForPayer(User $user, float $value)
    {
        if ($user->balance < $value) {
            throw new InvalidTransactionException('Insufficient funds for payer');
        }
    }

}