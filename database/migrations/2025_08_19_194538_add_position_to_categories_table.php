<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            if (!Schema::hasColumn('categories', 'position')) {
                $table->unsignedInteger('position')->default(0)->after('parent_id');
            }
            // Recommended for performance when ordering within a parent:
            $table->index(['parent_id', 'position']);
        });
    }
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropIndex(['parent_id', 'position']);
            $table->dropColumn('position');
        });
    }
};
