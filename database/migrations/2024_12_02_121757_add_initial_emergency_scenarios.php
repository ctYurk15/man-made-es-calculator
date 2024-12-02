<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddInitialEmergencyScenarios extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('emergency_scenarios')->insert([
            ['name' => 'Пожежа', 'description' => 'Можливість виникнення пожежі на об’єкті', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Вибух', 'description' => 'Ризик вибуху через технічну несправність або зовнішній фактор', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Розлив хімічних речовин', 'description' => 'Небезпека розливу небезпечних хімічних речовин', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Збої в роботі обладнання', 'description' => 'Технічні збої в роботі обладнання, що призводять до аварійних ситуацій', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('emergency_scenarios')->whereIn('name', [
            'Пожежа',
            'Вибух',
            'Розлив хімічних речовин',
            'Збої в роботі обладнання',
        ])->delete();
    }
}

