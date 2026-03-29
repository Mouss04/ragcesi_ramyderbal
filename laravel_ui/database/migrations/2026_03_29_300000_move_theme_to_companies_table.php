<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add theme columns to companies.
        Schema::table('companies', function (Blueprint $table): void {
            $table->string('logo')->nullable()->after('name');
            $table->string('theme_color', 20)->default('#0c7070')->after('logo');
        });

        // 2. Copy the one global site_setting row into the default company (id=1).
        $setting = DB::table('site_settings')->first();
        if ($setting) {
            DB::table('companies')->where('id', 1)->update([
                'name'        => $setting->company_name ?: DB::table('companies')->where('id', 1)->value('name'),
                'logo'        => $setting->company_logo,
                'theme_color' => $setting->theme_color ?? '#0c7070',
            ]);
        }

        // 3. Drop the now-redundant site_settings table.
        Schema::dropIfExists('site_settings');
    }

    public function down(): void
    {
        // Recreate site_settings with a single row from company 1.
        Schema::create('site_settings', function (Blueprint $table): void {
            $table->id();
            $table->string('company_name')->nullable();
            $table->string('company_logo')->nullable();
            $table->string('theme_color', 20)->default('#0c7070');
            $table->timestamps();
        });

        $company = DB::table('companies')->where('id', 1)->first();
        DB::table('site_settings')->insert([
            'company_name' => $company?->name,
            'company_logo' => $company?->logo,
            'theme_color'  => $company?->theme_color ?? '#0c7070',
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        Schema::table('companies', function (Blueprint $table): void {
            $table->dropColumn(['logo', 'theme_color']);
        });
    }
};
