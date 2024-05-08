<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class () extends Migration {

    public function up(): void
    {
        if(!schema()->hasColumn('', '')) {
            schema()->table('', function (Blueprint $table) {
            });
        }

        if(!schema()->hasTable('')) {
            schema()->create('', function (Blueprint $table) {
                $table->increments('id');
            });
        }
    }

    public function down(): void
    {
        schema()->drop('');
    }
};