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
        Schema::create('applicants', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('contact_number');
            $table->date('date_hired');
            $table->string('address');
            $table->date('date_birth');
            $table->string('place_birth');
            $table->string('passport_number');
            $table->string('passport_place_issue');
            $table->date('passport_date_issue');
            $table->date('passport_date_expired');
            $table->string('elementary');
            $table->string('high_school');
            $table->string('vocational');
            $table->string('college');
            $table->string('father_name');
            $table->string('father_occupation');
            $table->string('mother_name');
            $table->string('mother_occupation');
            $table->string('spouse_name');
            $table->string('spouse_occupation');
            $table->enum('gender', ['male', 'female']);
            $table->string('religion');
            $table->enum('civil_status', ['single', 'married', 'divorced', 'widowed']);
            $table->float('height', 8, 2);
            $table->float('weight', 8, 2);
            $table->text('objectives');
            $table->string('pic_face');
            $table->string('pic_body');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applicants');
    }
};
