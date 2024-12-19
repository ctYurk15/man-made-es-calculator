<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScenarioCalculationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scenario_calculations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('calculation_id');
            $table->unsignedBigInteger('scenario_id');
            $table->string('numeric_assessment');
            $table->string('text_assessment');
            $table->timestamps();

            $table->foreign('calculation_id')
                ->references('id')
                ->on('calculations_archive')
                ->onDelete('cascade');

            $table->foreign('scenario_id')
                ->references('id')
                ->on('emergency_scenarios')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('scenario_calculations');
    }
}
