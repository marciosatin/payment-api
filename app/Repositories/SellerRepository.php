<?php

namespace App\Repositories;

use App\Models\Seller;

class SellerRepository implements SellerRepositoryInterface
{
    
    public function create(array $data): array
    {
        $seller = Seller::create($data);
        return $seller->toArray();
    }

}