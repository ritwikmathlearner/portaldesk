<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->integer('total_word_count');
            $table->string('word_count_break');
            $table->string('country')->default('unknown');
            $table->string('reference_style')->default('unknown');
            $table->text('description');
            $table->timestamp('student_deadline');
            $table->timestamp('tutor_deadline');
            $table->text('requirement_path');
            $table->text('solution_path')->nullable();
            $table->timestamp('upload_date_time')->nullable();
            $table->string('status')->default('unproductive');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('is_allocated_to')->nullable();
            $table->timestamp('allocation_date_time')->nullable();
            $table->timestamps();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('is_allocated_to')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tasks');
    }
}
