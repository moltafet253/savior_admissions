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
        Schema::create('application_timings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('academic_year');
            $table->foreign('academic_year')->references('id')->on('academic_years');
            $table->enum('students_application_type', ['All' , 'Presently Studying'])->default('All');
            $table->date('start_date');
            $table->time('start_time');
            $table->date('end_date');
            $table->time('end_time');
            $table->integer('interview_time');
            $table->integer('delay_between_reserve');
            $table->json('interviewers');
            $table->float('fee');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('application_timings');
    }
};
