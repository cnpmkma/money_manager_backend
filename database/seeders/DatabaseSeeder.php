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
            ['category_name' => 'Ăn uống', 'type' => 'chi'],
            ['category_name' => 'Gia đình', 'type' => 'chi'],
            ['category_name' => 'Giải trí', 'type' => 'chi'],
            ['category_name' => 'Giáo dục', 'type' => 'chi'],
            ['category_name' => 'Hóa đơn', 'type' => 'chi'],
            ['category_name' => 'Sức khỏe', 'type' => 'chi'],
            ['category_name' => 'Lương', 'type' => 'thu'],
            ['category_name' => 'Tiền thưởng', 'type' => 'thu'],
            ['category_name' => 'Khác', 'type' => 'chi'],
        ];
        
        foreach ($categories as $cat) {
            Category::create($cat);
        }
        
        User::factory(5)->has(Wallet::factory(2)->has(Transaction::factory(5)))->create();
    }
}
