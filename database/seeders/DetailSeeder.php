<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Customer;
use Faker\Factory as Faker;

class DetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        $customerIds = Customer::pluck('id_customer');
        $now = date('Y-m-d H:i:s');

        foreach ($customerIds as $customerId) {
            for ($i = 1; $i <= 5; $i++) {
                DB::table('detail_customers')->insert([
                    'nama_brg' =>$faker->randomElement(['LAPTOP', 'MONITOR', 'MOTHERBOARD', 'IPHONE']),
                    'harga' => rand(0, 1000000),
                    'qty' => $faker->numberBetween(1, 20),
                    'customer_id' => $customerId,
                    'created_at' => $now,
                    'updated_at' => $now
                ]);
            }
        }
    }
}
