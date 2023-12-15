<?php

require_once __DIR__ . "/2021_02_16_100200_create_users_table.php";

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserRO extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(CreateUsersTable::TABLE_NAME, function (Blueprint $table) {
            CreateUsersTable::foreignTo($table, 'responsable_id')
                ->nullable()
                ->after('creator_id')
                ->comment("Responsable Operationel");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
