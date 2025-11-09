<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // USERS: заменяем id (integer) на uuid
        Schema::table('users', function (Blueprint $table) {
            // 1️⃣ Добавляем новый UUID столбец
            $table->uuid('uuid')->nullable()->after('id');
        });

        // 2️⃣ Генерируем UUID для уже существующих записей
        DB::table('users')->get()->each(function ($user) {
            DB::table('users')
                ->where('id', $user->id)
                ->update(['uuid' => (string) Str::uuid()]);
        });

        // 3️⃣ Делаем uuid основным ключом
        Schema::table('users', function (Blueprint $table) {
            $table->dropPrimary(['id']);
            $table->dropColumn('id');
            $table->uuid('id')->primary()->first();
        });

        // sessions: меняем user_id → uuid
        Schema::table('sessions', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropColumn('user_id');

            $table->uuid('user_id')->nullable()->index()->after('id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        // Возврат в int (если нужно)
        Schema::table('sessions', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
            $table->foreignId('user_id')->nullable()->index()->after('id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropPrimary(['id']);
            $table->dropColumn('id');
            $table->id()->first();
        });
    }
};
