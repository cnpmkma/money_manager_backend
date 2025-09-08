<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Wallet;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'amount' =>$this->faker->randomFloat(),
            'note' => $this->faker->sentence(),
            'type' => $this->faker->randomElement(['thu', 'chi']),
            'wallet_id' => Wallet::factory(),
            'category_id' => Category::pluck('id')->random()
        ];
    }
}
