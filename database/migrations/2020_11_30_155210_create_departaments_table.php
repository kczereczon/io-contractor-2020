<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDepartamentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('departaments', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->bigInteger("contractor_id")->unsigned();
            $table->string("street");
            $table->string("city");
            $table->string("postal_code");
            $table->string("country");
            $table->boolean("is_main")->default(false);
            $table->timestamps();
            $table->foreign('contractor_id')->references('id')->on('contractors')->onDelete('cascade');;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('departaments');
    }
}
