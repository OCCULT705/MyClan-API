<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSpousesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('spouses', function (Blueprint $table) {
            $table->id();
            $table->string('member_id', 100)->index();
            $table->foreign('member_id')->references('id')->on('members')->onDelete('cascade');
            $table->string('partner_id', 100)->index();
            $table->foreign('partner_id')->references('id')->on('members')->onDelete('cascade');
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
        Schema::dropIfExists('spouses');
    }
}
