<?php

use Alograg\Traits\SchemaUtils;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketsTable extends Migration
{
    use SchemaUtils;

    const TABLE_NAME = 'tickets';
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
            $statusComment = <<<TEXT
Status de projects [0-255], ej:
0=>disabled
TEXT;
            $table->unsignedTinyInteger('status')
                ->default(0)
                ->index()
                ->comment(self::setComment($statusComment));
            $priorityComment = <<<TEXT
Priority de projects [0-255], ej:
TEXT;
            $table->unsignedTinyInteger('priority')
                ->default(0)
                ->index()
                ->comment(self::setComment($priorityComment));
            $criticalityComment = <<<TEXT
Criticality de projects [0-255], ej:
TEXT;
            $table->unsignedTinyInteger('criticality')
                ->default(0)
                ->index()
                ->comment(self::setComment($criticalityComment));
            CreateServicesTable::foreignTo($table)
                ->comment("Identifiant de service");
            CreateUsersTable::foreignTo($table, 'creator_id')
                ->comment("Identifiant d'utilisateur createur");
            $table->timestamps();
            $table->softDeletes();
            $table->string('title')
                ->comment('Titre');
            $table->text('description')
                ->nullable()
                ->comment('Description');
            $table->json('participants')
                ->comment("Tableau des identifiants d'utilisateurs");
            $table->json('tags')
                ->comment("Tableau de references");
            $table->json('stats')
                ->comment("ticket stats");
            $table->engine = 'InnoDB';
        });
        self::setTableComment(self::TABLE_NAME, 'Incidents');
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
