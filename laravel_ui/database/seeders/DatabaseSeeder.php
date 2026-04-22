<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // The "Default Company" (id=1) and the witrack_agent user are already
        // inserted by migrations. Reuse the existing company for seeded users.
        $companyId = DB::table('companies')->value('id');

        // witrack_agent is seeded by the migration — skip it here to avoid duplicates.
        $users = [
            [
                'name'       => 'admin',
                'password'   => 'Admin@2026',
                'role'       => 'admin',
                'company_id' => $companyId,
            ],
            [
                'name'       => 'supervisor',
                'password'   => 'Supervisor@2026',
                'role'       => 'supervisor',
                'company_id' => $companyId,
            ],
            [
                'name'       => 'user',
                'password'   => 'User@2026',
                'role'       => 'user',
                'company_id' => $companyId,
            ],
        ];

        foreach ($users as $data) {
            User::firstOrCreate(
                ['name' => $data['name']],
                array_merge($data, ['created_at' => now(), 'updated_at' => now()])
            );
        }
    }
}
