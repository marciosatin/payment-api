<?php

namespace App\Repositories;

use App\Exceptions\InvalidSellerTypeException;
use App\Models\Seller;
use App\Models\User;
use App\Models\UserType;

class SellerRepository implements SellerRepositoryInterface
{

    public function create(array $data): array
    {

        $user = User::find($data['id_user']);
        $this->validateTypeUserSeller($user);

        $seller = Seller::create($data);
        return $seller->toArray();
    }

    private function validateTypeUserSeller(User $user)
    {
        if ($user->type->type_name == UserType::TYPE_COMUM) {
            throw new InvalidSellerTypeException('User must be a user type lojista');
        }
    }

}