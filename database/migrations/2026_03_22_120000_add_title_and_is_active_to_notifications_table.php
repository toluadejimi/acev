<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('notifications')) {
            Schema::create('notifications', function (Blueprint $table) {
                $table->id();
                $table->string('title')->nullable();
                $table->text('message')->nullable();
                $table->boolean('is_active')->default(false);
                $table->timestamps();
            });
            DB::table('notifications')->insert([
                'id' => 1,
                'title' => null,
                'message' => null,
                'is_active' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return;
        }

        Schema::table('notifications', function (Blueprint $table) {
            if (! Schema::hasColumn('notifications', 'title')) {
                $table->string('title')->nullable();
            }
            if (! Schema::hasColumn('notifications', 'is_active')) {
                $table->boolean('is_active')->default(true);
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('notifications')) {
            return;
        }

        Schema::table('notifications', function (Blueprint $table) {
            if (Schema::hasColumn('notifications', 'is_active')) {
                $table->dropColumn('is_active');
            }
            if (Schema::hasColumn('notifications', 'title')) {
                $table->dropColumn('title');
            }
        });
    }
};
