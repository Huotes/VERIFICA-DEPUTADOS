<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeputadosTable extends Migration
{
    public function up()
    {
        Schema::create('deputados', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('partido')->nullable();
            $table->string('uf', 2)->nullable();
            $table->string('email')->nullable();
            $table->string('telefone')->nullable();
            $table->string('url_foto')->nullable();
            $table->bigInteger('id_api')->unique();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('deputados');
    }
}
