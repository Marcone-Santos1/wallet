<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Infrastructure\Database\Eloquent\Wallet;

/**
 * @extends Factory<Wallet>
 */
class WalletFactory extends Factory
{
    /**
     * The current Model of the factory
     *
     * @var string
     */
    protected $model = Wallet::class;


    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [

        ];
    }
}
