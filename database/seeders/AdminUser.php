<?php
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUser extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@library.local'],
            [
                'full_name' => 'Admin',
                'phone' => '0123456789',
                'role' => 'admin',
            ]
        );
    }
}
