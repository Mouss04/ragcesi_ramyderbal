<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rag_histories', function (Blueprint $table): void {
            $table->string('chat_session_id', 36)->nullable()->index()->after('user_id');
        });
    }

    public function down(): void
    {
        Schema::table('rag_histories', function (Blueprint $table): void {
            $table->dropColumn('chat_session_id');
        });
    }
};
