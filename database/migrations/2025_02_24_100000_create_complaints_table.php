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
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();
            $table->string('foreign_recruitment_agency');
            $table->string('ofw_full_name');
            $table->string('gender');
            $table->date('birthdate');
            $table->string('occupation');
            $table->string('nation_id');
            $table->string('passport_no');
            $table->string('email');
            $table->string('contact_person');
            $table->string('primary_contact');
            $table->string('secondary_contact')->nullable();
            $table->text('address_abroad');
            $table->text('complaint'); // max 10000 chars enforced in validation
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};
