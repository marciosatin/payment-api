<?php

namespace App\Repositories;

use App\Models\Seller;
use App\Models\User;
use Exception;

class SellerRepository implements SellerRepositoryInterface
{
    
    public function create(array $data): array
    {
        
        try {
            $user = User::find($data['id_user']);
            if ($user->type->type_name == 'comum') {
                throw new Exception('User must be a user type lojista');
            }

            $seller = Seller::create($data);
            return $seller->toArray();
        } catch (Exception $exc) {
            return [
                'message' => $exc->getMessage()
            ];
        }

    }

}