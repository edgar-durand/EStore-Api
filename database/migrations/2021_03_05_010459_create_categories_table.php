<?php

use App\Category;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        Category::create([
            'name'=>'Tools',
            'description'=>'tools'
        ]);
        Category::create([
            'name'=>'Home Utils',
            'description'=>'home utils'
        ]);
        Category::create([
            'name'=>'Electronic',
            'description'=>'electronic'
        ]);
        Category::create([
            'name'=>'Computer components',
            'description'=>'PC items'
        ]);
        Category::create([
            'name'=>'Wood' ,
            'description'=>'woods jobs'
        ]);
        Category::create([
            'name'=>'Food',
            'description'=>'food, candies, etc.'
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('categories');
    }
}
