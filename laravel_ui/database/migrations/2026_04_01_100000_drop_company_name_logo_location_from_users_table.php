<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn(['company_name', 'company_logo', 'location']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('company_name')->nullable()->after('timezone');
            $table->string('company_logo')->nullable()->after('company_name');
            $table->string('location')->nullable()->after('theme_color');
        });
    }
};
