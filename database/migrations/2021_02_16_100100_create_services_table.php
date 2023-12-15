<?php

use Alograg\Traits\SchemaUtils;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServicesTable extends Migration
{
    use SchemaUtils;

    const TABLE_NAME = 'services';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table) {
            $table->charset = 'utf8';
            $table->collation = env('APP_COLLATION', 'utf8_unicode_ci');
            $table->unsignedInteger('id', true)
                ->comment('Identifiant');
            $table->timestamps();
            $table->softDeletes();
            $table->string('name')
                ->nullable()
                ->comment('Nom');
            $table->json('settings')
                ->nullable()
                ->comment('Réglages');
            $table->engine = 'InnoDB';
        });
        self::setTableComment(self::TABLE_NAME, 'Services enregistrés');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(self::TABLE_NAME);
    }
}
