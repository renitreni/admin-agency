<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('complaints', function (Blueprint $table) {
            $table->uuid('agency_uuid')->after('id');
            $table->foreign('agency_uuid')
                ->references('uuid')
                ->on('agencies')
                ->cascadeOnDelete();
            $table->index('agency_uuid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('complaints', function (Blueprint $table) {
            $table->dropForeign(['agency_uuid']);
            $table->dropIndex(['agency_uuid']);
            $table->dropColumn('agency_uuid');
        });
    }
};
