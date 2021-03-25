<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository implements UserRepositoryInterface
{
    
    public function create(array $data): array
    {
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        $user = User::create($data);
        return $user->toArray();
    }

    public function list(?string $q): array
    {
        if (isset($q)) {
            $users = User::where('full_name', 'like', "%{$q}%")->get();
        } else {
            $users = User::all();
        }
        
        return $users->toArray();
    }

    public function show(int $id): array
    {
        return User::findOrFail($id)->toArray();
    }

}