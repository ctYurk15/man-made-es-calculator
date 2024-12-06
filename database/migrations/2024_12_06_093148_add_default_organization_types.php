<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddDefaultOrganizationTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('organization_types')->insert([
            [
                'name' => 'Промисловість',
                'description' => 'Підприємства промислового сектора.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Хімічне виробництво',
                'description' => 'Організації, що займаються виробництвом хімічних речовин.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Енергетика',
                'description' => 'Компанії, що працюють у сфері виробництва та розподілу енергії.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Транспорт',
                'description' => 'Організації, що займаються перевезеннями.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('organization_types')->whereIn('name', [
            'Промисловість',
            'Хімічне виробництво',
            'Енергетика',
            'Транспорт',
        ])->delete();
    }
}
