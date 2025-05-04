<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Factories\ReferralCodeFactory;
use Database\Factories\UserFactory;
use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        UserFactory::$password = '123456';

        // Create Super Admin
        $superAgent = User::factory()->create([
            'email' => 'admin@demo.com',
            'phone' => '01757575757',
        ]);
        $superAgent->assignRole('admin');

//        UserFactory::times(20)->create();

//        $users = User::all();
//        $users->each(function ($user) use ($superAgent) {
//            ReferralCodeFactory::times(1)->create([
//                'user_id' => $user->id,
//            ]);
//
//            if ($user->id === $superAgent->id) {
//                return;
//            }
//
//            $user->assignRole('customer');
//        });

        //        // Create Super Admin
        //        $superAgent = User::factory()->create([
        //            'email' => 'customer@demo.com',
        //        ]);
        //        $superAgent->assignRole('customer');
        //
        //        // Create Staff
        //        User::factory()
        //            ->count(5)
        //            ->create()
        //            ->each(function ($user) {
        //                $user->assignRole('staff');
        //            });
        //
        //        $users = User::factory()
        //            ->count(10)
        //            ->create();
        //
        //        $users->each(function ($user) {
        //            $user->assignRole('customer');
        //        });
    }
}
