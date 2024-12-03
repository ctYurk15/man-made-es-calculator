<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmergencyScenarioOrganizationTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('emergency_scenario_organization_type', function (Blueprint $table) {
            $table->id();

            // Поля для зовнішніх ключів
            $table->unsignedBigInteger('emergency_scenario_id');
            $table->unsignedBigInteger('organization_type_id');

            // Визначення зовнішніх ключів
            $table->foreign('emergency_scenario_id', 'fk_emergency_scenario')
                ->references('id')->on('emergency_scenarios')
                ->onDelete('cascade');

            $table->foreign('organization_type_id', 'fk_organization_type')
                ->references('id')->on('organization_types')
                ->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('emergency_scenario_organization_type');
    }
}
