<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    // Renamear a classe para CreateLocacoesTable
    // Renamear a tabela    para locacoes (ajustar o model)
    // Renamear a migration para ...locacoes...
    // criar protected $table = 'rents'; em Model.php
    public function up()
    {
        Schema::create('rents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('car_id');
            $table->dateTime('period_start_date');
            $table->dateTime('period_end_date_expected');
            $table->dateTime('period_end_date_realized');
            $table->float('daily_value', 8,2);
            $table->integer('km_start');
            $table->integer('km_end');
            $table->timestamps();

            //foreign key (constraints)
            $table->foreign('customer_id')->references('id')->on('customers');
            $table->foreign('car_id')->references('id')->on('cars');
        });
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rents');
    }
}
