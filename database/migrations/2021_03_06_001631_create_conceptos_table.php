<?php

use App\Concepto;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConceptosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('conceptos', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->timestamps();
        });

        Concepto::create([
           'name'=>'OPEN ACCOUNT',
           'description'=> 'NEW ACCOUNT'
        ]);
        Concepto::create([
            'name'=>'PURCHASE',
            'description'=> 'NEW PURCHASE'
        ]);
        Concepto::create([
            'name'=>'SALE',
            'description'=> 'NEW SALE'
        ]);
        Concepto::create([
            'name'=>'TRANSFER',
            'description'=> 'MONEY TRANSFER'
        ]);
        Concepto::create([
            'name'=>'OTHER',
            'description'=> 'UNKNOWN'
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('conceptos');
    }
}
