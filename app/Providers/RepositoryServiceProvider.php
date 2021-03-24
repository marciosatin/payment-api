<?php

namespace App\Providers;

use App\Repositories\SellerRepository;
use App\Repositories\SellerRepositoryInterface;
use App\Repositories\UserRepository;
use App\Repositories\UserRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{

    /**
     * @inheritDoc
     */
    public function register()
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(SellerRepositoryInterface::class, SellerRepository::class);
    }

    /**
     * @inheritDoc
     */
    public function provides()
    {
        return [
            UserRepositoryInterface::class,
            SellerRepositoryInterface::class,
        ];
    }

}