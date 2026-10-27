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
        Schema::table('deployments', function (Blueprint $table) {
            // Add foreign key constraint with cascade on delete
            // (Currently only has an index, no FK constraint)
            $table->foreign('handler_id')
                ->references('id')
                ->on('handlers')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deployments', function (Blueprint $table) {
            // Drop the foreign key constraint
            $table->dropForeign(['handler_id']);
            // Index will remain
        });
    }
};
