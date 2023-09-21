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
        Schema::create('work_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('worker_id')->constrained(); // 'worker_id',
            $table->string('company_name'); // 'company_name',
            $table->text('address');    // 'address',
            $table->date('from_date');  // 'from_date',
            $table->date('to_date');    // 'to_date',
            $table->string('position'); // 'position'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_histories');
    }
};
