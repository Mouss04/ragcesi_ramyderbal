<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('avatar')->nullable()->after('role');
            $table->string('timezone')->default('Africa/Algiers')->after('avatar');
            $table->string('company_name')->nullable()->after('timezone');
            $table->string('company_logo')->nullable()->after('company_name');
            $table->string('theme_color', 20)->default('#0c7070')->after('company_logo');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn(['avatar', 'timezone', 'company_name', 'company_logo', 'theme_color']);
        });
    }
};
