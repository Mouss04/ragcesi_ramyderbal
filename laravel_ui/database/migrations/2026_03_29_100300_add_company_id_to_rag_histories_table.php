<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rag_histories', function (Blueprint $table): void {
            $table->foreignId('company_id')
                ->nullable()
                ->after('id')
                ->constrained()
                ->cascadeOnDelete();
        });

        // Derive company_id from the user who owns each history record.
        $histories = DB::table('rag_histories')->whereNull('company_id')->get();
        foreach ($histories as $history) {
            $user = DB::table('users')->find($history->user_id);
            if ($user && $user->company_id) {
                DB::table('rag_histories')
                    ->where('id', $history->id)
                    ->update(['company_id' => $user->company_id]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('rag_histories', function (Blueprint $table): void {
            $table->dropForeign(['company_id']);
            $table->dropColumn('company_id');
        });
    }
};
