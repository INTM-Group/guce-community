<?php

use Alograg\Traits\SchemaUtils;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRolesTable extends Migration
{
    use SchemaUtils;

    const TABLE_NAME = 'roles';

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
            $typeComment = <<<TEXT
Types de roles, ej:
0=>"disable"
1=>"client"
2=>"supplier"
4=>"service"
8=>"manager"
TEXT;
            $table->unsignedTinyInteger('type')
                ->default(0)
                ->index()
                ->comment(self::setComment($typeComment));
            $table->string('slug')
                ->unique()
                ->comment('Nom inter-langue en i18n');
            $table->timestamps();
            $table->softDeletes();
            $table->json('description')
                ->comment('Description: i18n format');
            $table->json('permissions')
                ->nullable()
                ->comment('Les permissions');
            $table->engine = 'InnoDB';
        });
        self::setTableComment(self::TABLE_NAME, "Profils d'utilisateurs");
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
