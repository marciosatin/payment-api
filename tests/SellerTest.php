<?php

use App\Models\User;
use App\Models\UserType;
use Illuminate\Http\Response;
use Laravel\Lumen\Testing\DatabaseMigrations;

class SellerTest extends TestCase
{

    use DatabaseMigrations;
    
    private $sellerRepository;
    private $userFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sellerRepository = $this->app->make('App\Repositories\SellerRepository');
        $this->userFactory = $this->app->make('Database\Factories\UserFactory');
    }

    public function testCreateSellerData()
    {
        $typeLojista = UserType::create([
                    'type_name' => UserType::TYPE_LOJISTA
        ]);
        $this->seeInDatabase('users_types', ['type_name' => UserType::TYPE_LOJISTA]);
        
        $userFake = $this->userFactory->make();
        $userFake->id_type = $typeLojista->id;

        $userData = $userFake->toArray();
        $userData['password'] = 'teste123';

        $user = User::create($userData);

        $responseData = $this->sellerRepository->create([
            'cnpj' => '56458452000148',
            'id_user' => $user->id
        ]);

        $this->seeInDatabase('sellers', [
            'cnpj' => $responseData['cnpj'],
            'id_user' => $responseData['id_user'],
        ]);
    }

    public function testCreateSellerDuplicateCnpj()
    {
        $typeLojista = UserType::create([
                    'type_name' => UserType::TYPE_LOJISTA
        ]);
        $this->seeInDatabase('users_types', ['type_name' => UserType::TYPE_LOJISTA]);
        
        $userFake = $this->userFactory->make();
        $userFake->id_type = $typeLojista->id;

        $userData = $userFake->toArray();
        $userData['password'] = 'teste123';

        $user = User::create($userData);

        $responseData = $this->sellerRepository->create([
            'cnpj' => '56458452000148',
            'id_user' => $user->id
        ]);

        $this->seeInDatabase('sellers', [
            'cnpj' => $responseData['cnpj'],
            'id_user' => $responseData['id_user'],
        ]);

        $this->json('POST', '/users/sellers', $responseData)
                ->seeStatusCode(Response::HTTP_BAD_REQUEST)
                ->seeJsonEquals([
                    'cnpj' => ['The cnpj has already been taken.']
        ]);
    }

    public function testCreateSellerInvalidType()
    {
        $typeComum = UserType::create([
                    'type_name' => UserType::TYPE_COMUM
        ]);
        $this->seeInDatabase('users_types', ['type_name' => UserType::TYPE_COMUM]);

        $user = User::create([
                    'full_name' => 'user comum',
                    'email' => 'usercomum1@email.com',
                    'cpf' => '89545612321',
                    'password' => 'teste123',
                    'balance' => 100,
                    'id_type' => $typeComum->id
        ]);

        $this->json('POST', '/users/sellers', [
                    'cnpj' => '56458452000148',
                    'id_user' => $user->id,
                ])
                ->seeStatusCode(Response::HTTP_BAD_REQUEST)
                ->seeJsonEquals([
                    'code' => Response::HTTP_BAD_REQUEST,
                    'message' => 'User must be a user type lojista',
        ]);
    }

}