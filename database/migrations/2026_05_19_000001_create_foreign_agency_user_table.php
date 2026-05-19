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
        Schema::create('foreign_agency_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('foreign_agency_id')->constrained('foreign_agencies');
            $table->foreignId('user_id')->constrained('users');
            $table->timestamps();
            $table->unique(['foreign_agency_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('foreign_agency_user');
    }
};
