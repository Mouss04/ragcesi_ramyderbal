<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Seed the one global witrack_agent account (no company).
        // Default password: Witrack@2026  — change it immediately after first login.
        if (! DB::table('users')->where('role', 'witrack_agent')->exists()) {
            DB::table('users')->insert([
                'name'       => 'witrack',
                'password'   => Hash::make('Witrack@2026'),
                'role'       => 'witrack_agent',
                'company_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('users')->where('role', 'witrack_agent')->delete();
    }
};
