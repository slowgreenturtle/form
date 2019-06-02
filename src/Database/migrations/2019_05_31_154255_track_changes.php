<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TrackChanges extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('model_changes', function (Blueprint $table)
        {

            $table->bigIncrements('id');
            $table->unsignedBigInteger('reportable_id');
            $table->string('reportable_type');
            $table->string('field');
            $table->text('value')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();

            $table->timestamp('created_at');
        });

    }

}
