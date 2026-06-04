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
        Schema::table('foreign_agencies', function (Blueprint $table) {
            $table->string('primary_contact_number')->nullable()->after('name');
            $table->text('address')->nullable()->after('primary_contact_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('foreign_agencies', function (Blueprint $table) {
            $table->dropColumn(['primary_contact_number', 'address']);
        });
    }
};
