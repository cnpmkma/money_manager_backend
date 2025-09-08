<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        $categories = [
            'Ăn uống',
            'Gia đình',
            'Giải trí',
            'Giáo dục',
            'Hóa đơn',
            'Sức khỏe',
        ];
        
        foreach ($categories as $name) {
            Category::create(['category_name' => $name]);
        }
        
        User::factory(5)->has(Wallet::factory(2)->has(Transaction::factory(5)))->create();
    }
}
