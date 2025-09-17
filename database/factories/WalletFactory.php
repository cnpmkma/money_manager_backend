<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Wallet>
 */
class WalletFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'wallet_name' => $this->faker->word() . ' Wallet',
            'balance' => $this->faker->randomFloat(2,0, 1000000),
            'skin_index' => $this->faker->numberBetween(1, 12),
            'user_id' => User::factory()
        ];
    }
}
