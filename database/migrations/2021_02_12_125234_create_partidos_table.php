<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartidosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('partidos', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('equipo1_id');
            $table->foreign('equipo1_id')->references('id')->on('equipos');
            $table->unsignedBigInteger('equipo2_id');
            $table->foreign('equipo2_id')->references('id')->on('equipos');
            $table->date('fecha');
            $table->time('hora');
            $table->unsignedBigInteger('liga_id');
            $table->foreign('liga_id')->references('liga_id')->on('equipos');
            $table->integer('resultadoEquipo1')->nullable();
            $table->integer('resultadoEquipo2')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('partidos');
    }
}
