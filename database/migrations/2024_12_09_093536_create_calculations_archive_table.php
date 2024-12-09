<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCalculationsArchiveTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('calculations_archive', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('organization_id'); // Посилання на організацію
            $table->year('year'); // Рік
            $table->string('numeric_assessment'); // Числове значення (VARCHAR)
            $table->string('text_assessment'); // Текстове значення (VARCHAR)
            $table->timestamps();

            // Зовнішній ключ на таблицю організацій
            $table->foreign('organization_id')
                ->references('id')
                ->on('organizations')
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
        Schema::dropIfExists('calculations_archive');
    }
}
