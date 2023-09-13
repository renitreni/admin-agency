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
        Schema::create('job_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agency_id')->constrained();   // 'agency_id',
            $table->string('posted_by');  // 'posted_by',
            $table->string('title'); // 'title',
            $table->uuid(); // 'uuid',
            $table->text('description'); // 'description',
            $table->string('country'); // 'country'
            $table->integer('is_published')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_posts');
    }
};
