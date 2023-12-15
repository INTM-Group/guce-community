<?php

use Alograg\Traits\SchemaUtils;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    use SchemaUtils;

    const TABLE_NAME = 'users';

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
Types de utilisateurs, ej:
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
            $table->uuid('remember_token')
                ->nullable()
                ->index()
                ->comment('Jeton personnalisé de session');
            $table->string('email')
                ->unique()
                ->comment('Courrier électronique');
            CreateServicesTable::foreignTo($table)
                ->nullable()
                ->comment("Identifiant de service");
            $table->unsignedInteger('creator_id')
                ->nullable()
                ->comment('Utilisateur createur');
            $table->timestamps();
            $table->softDeletes();
            $table->timestamp('last_login')
                ->nullable()
                ->comment('Dernier access');
            $table->string('password')
                ->comment('Mot de passe');
            $table->string('first_name')
                ->nullable()
                ->comment('Prenoms');
            $table->string('last_name')
                ->nullable()
                ->comment('Noms');
            $table->json('permissions')
                ->nullable()
                ->comment('Les permissions');
            $table->json('preferences')
                ->nullable()
                ->comment("Préférences de l'utilisateur");
            $table->engine = 'InnoDB';
        });
        self::setTableComment(self::TABLE_NAME, 'Utilisateurs enregistrés');
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
