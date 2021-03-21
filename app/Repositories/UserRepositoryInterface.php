<?php

namespace App\Repositories;

interface UserRepositoryInterface
{

    public function list(string $q): array;

    public function show(int $id): array;

    public function create(array $data): array;
}