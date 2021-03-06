<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;
    
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'full_name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => \Illuminate\Support\Facades\Hash::make('fkdskskUUskd89s8d8fsdjhfkstyry'),
            'balance' => $this->faker->randomFloat(2, 10, 10000),
            'cpf' => strval(rand(10000000000, 99999999999))
        ];
    }

}