<?php

use App\Models\User;
use App\Models\UserType;
use Illuminate\Http\Response;
use Laravel\Lumen\Testing\DatabaseMigrations;

class TransactionTest extends TestCase
{

    use DatabaseMigrations;

    private $transaction;

    private $userFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->transaction = $this->app->make('App\Services\TransactionService');
        $this->userFactory = $this->app->make('Database\Factories\UserFactory');
    }

    public function testCreateTransactionSuccessUserToUser()
    {
        $payer = $this->createUserByType(UserType::TYPE_COMUM);
        $payee = $this->createUserByType(UserType::TYPE_COMUM);

        $responseData = $this->transaction->create([
            'payer_id' => $payer->id,
            'payee_id' => $payee->id,
            'value' => 1
        ]);

        $this->seeInDatabase('transactions', [
            'id' => $responseData['id'],
            'payer_id' => $responseData['payer_id'],
            'payee_id' => $responseData['payee_id'],
            'value' => $responseData['value'],
        ]);
    }

    public function testCreateTransactionSuccessUserToSeller()
    {
        $payer = $this->createUserByType(UserType::TYPE_COMUM);
        $payee = $this->createUserByType(UserType::TYPE_LOJISTA);

        $responseData = $this->transaction->create([
            'payer_id' => $payer->id,
            'payee_id' => $payee->id,
            'value' => 1
        ]);

        $this->seeInDatabase('transactions', [
            'id' => $responseData['id'],
            'payer_id' => $responseData['payer_id'],
            'payee_id' => $responseData['payee_id'],
            'value' => $responseData['value'],
        ]);
    }

    public function testCreateTransactionInvalidPayerEqualsPayee()
    {
        $payer = $this->createUserByType(UserType::TYPE_COMUM);

        $this->json('POST', '/transactions', [
                    'payer_id' => $payer->id,
                    'payee_id' => $payer->id,
                    'value' => 1
                ])->seeStatusCode(Response::HTTP_BAD_REQUEST)
                ->seeJsonEquals([
                    'code' => Response::HTTP_BAD_REQUEST,
                    'message' => 'Invalid transaction for payer and payee'
        ]);
    }

    public function testCreateTransactionSellerUserNotAllowed()
    {
        $payer = $this->createUserByType(UserType::TYPE_LOJISTA);
        $payee = $this->createUserByType(UserType::TYPE_COMUM);

        $this->json('POST', '/transactions', [
                    'payer_id' => $payer->id,
                    'payee_id' => $payee->id,
                    'value' => 1
                ])->seeStatusCode(Response::HTTP_BAD_REQUEST)
                ->seeJsonEquals([
                    'code' => Response::HTTP_BAD_REQUEST,
                    'message' => 'Transaction not allowed for that user'
        ]);
    }

    public function testCreateTransactionValueMustBeGreaterThanZero()
    {
        $payer = $this->createUserByType(UserType::TYPE_COMUM);
        $payee = $this->createUserByType(UserType::TYPE_COMUM);

        $this->json('POST', '/transactions', [
                    'payer_id' => $payer->id,
                    'payee_id' => $payee->id,
                    'value' => 0
                ])->seeStatusCode(Response::HTTP_BAD_REQUEST)
                ->seeJsonEquals([
                    'value' => ['The value must be greater than 0.']
        ]);
    }

    public function testCreateTransactionInsufficientFoundsForPayer()
    {
        $payee = $this->createUserByType(UserType::TYPE_COMUM);

        $typeComum = UserType::create([
                    'type_name' => UserType::TYPE_COMUM
        ]);

        $userFake = $this->userFactory->make();
        $userFake->id_type = $typeComum->id;

        $userData = $userFake->toArray();
        $userData['password'] = 'teste123';
        $userData['balance'] = 0.00;

        $payer = User::create($userData);

        $this->json('POST', '/transactions', [
                    'payer_id' => $payer->id,
                    'payee_id' => $payee->id,
                    'value' => 1
                ])->seeStatusCode(Response::HTTP_BAD_REQUEST)
                ->seeJsonEquals([
                    'code' => Response::HTTP_BAD_REQUEST,
                    'message' => 'Insufficient funds for payer'
        ]);
    }

    private function createUserByType(string $typeName)
    {
        $typeComum = UserType::create([
                    'type_name' => $typeName
        ]);

        $userFake = $this->userFactory->make();
        $userFake->id_type = $typeComum->id;

        $userData = $userFake->toArray();
        $userData['password'] = 'teste123';

        return User::create($userData);
    }

}