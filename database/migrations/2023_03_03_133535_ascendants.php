<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Ascendants extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ascendants', function (Blueprint $table) {
            $table->id();
            $table->string('member_id', 100)->index();
            $table->foreign('member_id')->references('id')->on('members')->onDelete('cascade');
            $table->string('ascendant_id', 100)->index();
            $table->foreign('ascendant_id')->references('id')->on('members')->onDelete('cascade');
            $table->string('relationship', 50);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ascendants');
    }
}
