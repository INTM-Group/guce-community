<?php

use Alograg\Traits\SchemaUtils;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoleUserTable extends Migration
{
    use SchemaUtils;

    const TABLE_NAME = 'role_user';

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
            CreateUsersTable::foreignTo($table)
                ->comment("Identifiant d'utilisateur");
            CreateRolesTable::foreignTo($table)
                ->comment("Identifiant de roles");
            $table->timestamps();
            $table->unique(['user_id', 'role_id'], 'relationId');
            $table->engine = 'InnoDB';
        });
        self::setTableComment(self::TABLE_NAME, "Relation d'utilisateurs avec roles");
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
