<?php

use App\Models\UserType;
use Illuminate\Http\Response;
use Laravel\Lumen\Testing\DatabaseMigrations;

class UserTest extends TestCase
{

    use DatabaseMigrations;
    
    private $userRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userRepository = $this->app->make('App\Repositories\UserRepository');
    }

    public function testCreateSellerUser()
    {
        $typeLojista = UserType::create([
                    'type_name' => UserType::TYPE_LOJISTA
        ]);
        $this->seeInDatabase('users_types', ['type_name' => UserType::TYPE_LOJISTA]);

        $responseData = $this->userRepository->create([
            'full_name' => 'user lojista',
            'email' => 'userlojista@email.com',
            'cpf' => '00665489977',
            'password' => 'teste',
            'id_type' => $typeLojista->id
        ]);

        $this->seeInDatabase('users', [
            'cpf' => $responseData['cpf'],
            'email' => $responseData['email'],
        ]);
    }

    public function testCreateComumUser()
    {
        $typeComum = UserType::create([
                    'type_name' => UserType::TYPE_COMUM
        ]);
        $this->seeInDatabase('users_types', ['type_name' => UserType::TYPE_COMUM]);

        $responseData = $this->userRepository->create([
            'full_name' => 'user comum',
            'email' => 'usercomum@email.com',
            'cpf' => '89545612321',
            'password' => 'teste123',
            'id_type' => $typeComum->id
        ]);

        $this->seeInDatabase('users', [
            'cpf' => $responseData['cpf'],
            'email' => $responseData['email'],
        ]);
    }

    public function testCreateUserDuplicateCpf()
    {
        $typeComum = UserType::create([
                    'type_name' => UserType::TYPE_COMUM
        ]);
        $this->seeInDatabase('users_types', ['type_name' => UserType::TYPE_COMUM]);

        $dataUser = [
            'full_name' => 'user comum',
            'email' => 'usercomum1@email.com',
            'cpf' => '89545612321',
            'password' => 'teste123',
            'id_type' => $typeComum->id
        ];

        $this->userRepository->create($dataUser);

        $dataUser['email'] = 'usercomum2@email.com';

        $this->json('POST', '/users', $dataUser)
                ->seeStatusCode(Response::HTTP_BAD_REQUEST)
                ->seeJsonEquals([
                    'cpf' => ['The cpf has already been taken.'],
        ]);
    }

    public function testCreateUserDuplicateEmail()
    {
        $typeComum = UserType::create([
                    'type_name' => UserType::TYPE_COMUM
        ]);
        $this->seeInDatabase('users_types', ['type_name' => UserType::TYPE_COMUM]);

        $dataUser = [
            'full_name' => 'user comum',
            'email' => 'usercomum1@email.com',
            'cpf' => '89545612321',
            'password' => 'teste123',
            'id_type' => $typeComum->id
        ];

        $this->userRepository->create($dataUser);

        $dataUser['cpf'] = '89545612322';

        $this->json('POST', '/users', $dataUser)
                ->seeStatusCode(Response::HTTP_BAD_REQUEST)
                ->seeJsonEquals([
                    'email' => ['The email has already been taken.'],
        ]);
    }

}