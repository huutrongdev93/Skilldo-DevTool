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
                $table->string('title')->nullable();
                $table->integer('order')->default(0);
                $table->integer('user_created')->default(0);
                $table->integer('user_updated')->default(0);
                $table->dateTime('created')->default(DB::raw('CURRENT_TIMESTAMP'));
                $table->dateTime('updated')->nullable();
            });
        }
        if(!schema()->hasTable('MODEL_TABLE_NAME_metadata')) {
            schema()->create('MODEL_TABLE_NAME_metadata', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('object_id')->default(0);
                $table->string('meta_key', 100)->collation('utf8mb4_unicode_ci')->nullable();
                $table->longText('meta_value')->collation('utf8mb4_unicode_ci')->nullable();
                $table->integer('order')->default(0);
                $table->dateTime('created')->default(DB::raw('CURRENT_TIMESTAMP'));
                $table->dateTime('updated')->nullable();
                $table->index('object_id');
                $table->index('meta_key');
            });
        }
    }

    public function down(): void
    {
        schema()->drop('MODEL_TABLE_NAME');
        schema()->drop('MODEL_TABLE_NAME_metadata');
    }
};