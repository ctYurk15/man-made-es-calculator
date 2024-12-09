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
            $table->unsignedBigInteger('calculation_id'); // Посилання на calculations_archive
            $table->unsignedBigInteger('scenario_id'); // Посилання на emergency_scenarios
            $table->string('numeric_assessment'); // Числове значення (VARCHAR)
            $table->string('text_assessment'); // Текстове значення (VARCHAR)
            $table->timestamps();

            // Зовнішній ключ на таблицю calculations_archive
            $table->foreign('calculation_id')
                ->references('id')
                ->on('calculations_archive')
                ->onDelete('cascade');

            // Зовнішній ключ на таблицю emergency_scenarios
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
