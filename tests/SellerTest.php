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
        
        $user = $this->createUserByType(UserType::TYPE_LOJISTA);

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
        $user = $this->createUserByType(UserType::TYPE_LOJISTA);

        $responseData = $this->sellerRepository->create([
            'cnpj' => '56458452000148',
            'id_user' => $user->id
        ]);

        $this->json('POST', '/users/sellers', $responseData)
                ->seeStatusCode(Response::HTTP_BAD_REQUEST)
                ->seeJsonEquals([
                    'cnpj' => ['The cnpj has already been taken.']
        ]);
    }

    public function testCreateSellerInvalidType()
    {
        $user = $this->createUserByType(UserType::TYPE_COMUM);

        $this->json('POST', '/users/sellers', [
                    'cnpj' => '56458452000148',
                    'id_user' => $user->id,
                ])
                ->seeStatusCode(Response::HTTP_BAD_REQUEST)
                ->seeJsonEquals([
                    'id_user' => ['User must be a user type lojista'],
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