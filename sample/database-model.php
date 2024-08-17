<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Capsule\Manager as DB;

return new class () extends Migration {

    public function up(): void
    {
        if(!schema()->hasTable('MODEL_TABLE_NAME')) {
            schema()->create('MODEL_TABLE_NAME', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('order')->default(0);
                $table->integer('user_created')->default(0);
                $table->integer('user_updated')->default(0);
                $table->dateTime('created')->default(DB::raw('CURRENT_TIMESTAMP'));
                $table->dateTime('updated')->nullable();
            });
        }
    }

    public function down(): void
    {
        schema()->drop('MODEL_TABLE_NAME');
    }
};