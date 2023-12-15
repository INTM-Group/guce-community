<?php

use Illuminate\Database\Migrations\Migration;

class CreateGetUserIdByMAilFunction extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->down();
        $sql = <<<SQL
CREATE FUNCTION getUserIdByMail (param VARCHAR(255)) RETURNS INT
BEGIN
    DECLARE userId INT;
    SELECT id
        INTO userId
        FROM users
        WHERE email = param;

    RETURN COALESCE(userId, 1);
END;
SQL;
        \DB::unprepared($sql);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $sql = <<<SQL
DROP FUNCTION IF EXISTS getUserIdByMail;
SQL;
        return \DB::unprepared($sql);
    }
}
