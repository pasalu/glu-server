<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->increments('jobID');
            $table->timestamps();
            $table->string('submitterID');
            $table->string('processorID')->nullable();
            $table->enum('status', ['SUBMITTED', 'PROCESSING', 'FINISHED']);
            $table->string('command');
            $table->dateTime('submittedOn');
            $table->integer('priority');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('jobs');
    }
}
