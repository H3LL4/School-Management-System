<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 10)->default('staff')->after('password');   // admin or staff
            $table->unsignedTinyInteger('failed_attempts')->default(0)->after('role');
            $table->timestamp('locked_until')->nullable()->after('failed_attempts');
        });

        // Safety net: if users already exist, the oldest one becomes the admin.
        $first = DB::table('users')->orderBy('id')->first();
        if ($first) {
            DB::table('users')->where('id', $first->id)->update(['role' => 'admin']);
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'failed_attempts', 'locked_until']);
        });
    }
};
