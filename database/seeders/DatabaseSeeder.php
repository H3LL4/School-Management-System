<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        ActivityLog::$enabled = false;   // keep the audit trail clean while seeding

        User::updateOrCreate(['email' => 'admin@gmail.com'], [
            'name' => 'School Admin', 'password' => Hash::make('123456'), 'role' => 'admin',
        ]);
        User::updateOrCreate(['email' => 'staff@gmail.com'], [
            'name' => 'School Staff', 'password' => Hash::make('123456'), 'role' => 'staff',
        ]);

        $this->call([SchoolSeeder::class, AcademicSeeder::class]);

        ActivityLog::$enabled = true;
    }
}
