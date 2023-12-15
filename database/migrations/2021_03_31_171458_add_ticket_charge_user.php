<?php
require_once __DIR__ . "/2021_02_16_100400_create_tickets_table.php";
require_once __DIR__ . "/2021_02_16_100200_create_users_table.php";

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTicketChargeUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(CreateTicketsTable::TABLE_NAME, function (Blueprint $table) {
            CreateUsersTable::foreignTo($table, 'take_by')
                ->nullable()
                ->after('creator_id')
                ->comment("Identifiant d'utilisateur en charge");
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
