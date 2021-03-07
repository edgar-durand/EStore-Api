<?php

use App\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('status_message')->nullable();
            $table->longblob('photo')->nullable();

            $table->boolean('is_root')->default('0');
            $table->boolean('is_active')->default('1');

            $table->string('street')->nullable();
            $table->string('building')->nullable();
            $table->string('number')->nullable();
            $table->string('between')->nullable();
            $table->string('municipality')->nullable();
            $table->string('province')->nullable();

            $table->string('facebook')->nullable();
            $table->string('twitter')->nullable();
            $table->string('instagram')->nullable();

            $table->timestamps();
            $table->string('password');
            $table->string('api_token')->nullable();
//            $table->rememberToken();
        });

        factory(User::class)->times(1000)->create();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
