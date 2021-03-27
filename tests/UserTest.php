<?php

use App\Models\UserType;
use Illuminate\Http\Response;
use Laravel\Lumen\Testing\DatabaseMigrations;

class UserTest extends TestCase
{

    use DatabaseMigrations;
    
    private $userRepository;
    
    private $userFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userRepository = $this->app->make('App\Repositories\UserRepository');
        $this->userFactory = $this->app->make('Database\Factories\UserFactory');
    }

    public function testCreateSellerUser()
    {
        $userData = $this->getDataUserByType(UserType::TYPE_LOJISTA);

        $responseData = $this->userRepository->create($userData);

        $this->seeInDatabase('users', [
            'cpf' => $responseData['cpf'],
            'email' => $responseData['email'],
        ]);
    }

    public function testCreateComumUser()
    {
        $userData = $this->getDataUserByType(UserType::TYPE_COMUM);

        $responseData = $this->userRepository->create($userData);

        $this->seeInDatabase('users', [
            'cpf' => $responseData['cpf'],
            'email' => $responseData['email'],
        ]);
    }

    public function testCreateUserDuplicateCpf()
    {
        $userData = $this->getDataUserByType(UserType::TYPE_COMUM);

        $this->userRepository->create($userData);

        $userData['email'] = 'usercomum2@email.com';

        $this->json('POST', '/users', $userData)
                ->seeStatusCode(Response::HTTP_BAD_REQUEST)
                ->seeJsonEquals([
                    'cpf' => ['The cpf has already been taken.'],
        ]);
    }

    public function testCreateUserDuplicateEmail()
    {
        $userData = $this->getDataUserByType(UserType::TYPE_COMUM);

        $this->userRepository->create($userData);

        $userData['cpf'] = '89545612322';

        $this->json('POST', '/users', $userData)
                ->seeStatusCode(Response::HTTP_BAD_REQUEST)
                ->seeJsonEquals([
                    'email' => ['The email has already been taken.'],
        ]);
    }
    
    public function testShowUserNotFound()
    {
        $this->json('GET', '/users/0', [])
                ->seeStatusCode(Response::HTTP_NOT_FOUND)
                ->seeJsonEquals([
                    'user' => ['User not found'],
        ]);
    }

    private function getDataUserByType(string $typeName)
    {
        $typeComum = UserType::create([
                    'type_name' => $typeName
        ]);
        $this->seeInDatabase('users_types', ['type_name' => $typeName]);

        $user = $this->userFactory->make();
        $user->id_type = $typeComum->id;

        $userData = $user->toArray();
        $userData['password'] = 'teste123';

        return $userData;
    }

}