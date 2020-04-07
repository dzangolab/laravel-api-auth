<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuthProfilesTable extends Migration
{
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('profiles');
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->string('surname')->nullable(true);
            $table->string('given_name')->nullable(true);
            $table->string('middle_name')->nullable(true);
            $table->boolean('surname_first')->nullable(false)->default(false);
            $table->boolean('gender')->nullable();

            $table->foreign('id')
                ->references('id')->on('users');
        });
    }
}
