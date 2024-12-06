<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class SeedEmergencyScenarioOrganizationType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Отримуємо типи організацій за назвою
        $organizationTypes = DB::table('organization_types')->get()->pluck('id', 'name');

        // Отримуємо надзвичайні ситуації за назвою
        $emergencyScenarios = DB::table('emergency_scenarios')->get()->pluck('id', 'name');

        // Встановлення зв'язків
        $relationships = [
            'Пожежа' => ['Промисловість', 'Енергетика', 'Транспорт'],
            'Вибух' => ['Хімічне виробництво', 'Промисловість'],
            'Розлив хімічних речовин' => ['Хімічне виробництво'],
            'Збої в роботі обладнання' => ['Промисловість', 'Енергетика'],
        ];

        $data = [];
        foreach ($relationships as $scenarioName => $typeNames) {
            foreach ($typeNames as $typeName) {
                if (isset($emergencyScenarios[$scenarioName], $organizationTypes[$typeName])) {
                    $data[] = [
                        'emergency_scenario_id' => $emergencyScenarios[$scenarioName],
                        'organization_type_id' => $organizationTypes[$typeName],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
        }

        DB::table('emergency_scenario_organization_type')->insert($data);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Видалення тільки тих записів, які додалися цією міграцією
        $relationships = [
            'Пожежа' => ['Промисловість', 'Енергетика', 'Транспорт'],
            'Вибух' => ['Хімічне виробництво', 'Промисловість'],
            'Розлив хімічних речовин' => ['Хімічне виробництво'],
            'Збої в роботі обладнання' => ['Промисловість', 'Енергетика'],
        ];

        $organizationTypes = DB::table('organization_types')->get()->pluck('id', 'name');
        $emergencyScenarios = DB::table('emergency_scenarios')->get()->pluck('id', 'name');

        foreach ($relationships as $scenarioName => $typeNames) {
            foreach ($typeNames as $typeName) {
                if (isset($emergencyScenarios[$scenarioName], $organizationTypes[$typeName])) {
                    DB::table('emergency_scenario_organization_type')
                        ->where('emergency_scenario_id', $emergencyScenarios[$scenarioName])
                        ->where('organization_type_id', $organizationTypes[$typeName])
                        ->delete();
                }
            }
        }
    }
}
