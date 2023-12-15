<?php

require_once __DIR__ . "/2021_02_16_100200_create_users_table.php";

use Alograg\Traits\SchemaUtils;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreatePersistencesTable extends Migration
{
    use SchemaUtils;

    const TABLE_NAME = 'persistences';

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
            $table->uuid('id')
                ->primary()
                ->comment('Token');
            CreateUsersTable::foreignTo($table)
                ->comment("Identifiant d'utilisateur");
            $table->unsignedInteger('connected')
                ->default(0)
                ->comment('Jours connectés');
            $table->unsignedInteger('consecutive')
                ->default(0)
                ->comment('Jours consécutifs connectés');
            $table->timestamps();
            $table->engine = 'InnoDB';
        });
        self::setTableComment(self::TABLE_NAME, "Sessions d'utilisateurs");
        $this->createUsersStatsView();
    }

    /**
     * Función 'createUsersStatsView'
     *
     * @return null
     */
    public function createUsersStatsView()
    {
        $sqlView = <<<SQL
CREATE VIEW if not exists user_stats AS
    SELECT user_id,
        count(id) as loginCount,
        AVG(connected) as avgConected,
        min(consecutive) as minConsecutive,
        max(consecutive) as maxConsecutive,
        avg(consecutive) as avgConsecutive
    FROM persistences
    GROUP BY user_id;
SQL;
        DB::unprepared($sqlView);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->removeUsersStatsView();
        Schema::dropIfExists(self::TABLE_NAME);
    }

    /**
     * Función 'removeUsersStatsView'
     *
     * @return null
     */
    public function removeUsersStatsView()
    {
        $sqlView = <<<SQL
DROP VIEW IF EXISTS user_stats;
SQL;
        DB::unprepared($sqlView);
    }
}
