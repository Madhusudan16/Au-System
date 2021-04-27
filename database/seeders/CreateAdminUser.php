<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use App\Enums\UserType;
use App\Models\User;

class CreateAdminUser extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $adminData = [
            "name" => "Admin",
            "user_name" => "admin",
            "email" => "au_system@yopmail.com",
            "email_verified_at" => Carbon::now(),
            "password" => bcrypt("Admin@123"),
            "user_role" => UserType::Admin,
        ];
        User::create($adminData);
    }
}
