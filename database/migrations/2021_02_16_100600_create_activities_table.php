<?php

use Alograg\Traits\SchemaUtils;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivitiesTable extends Migration
{
    use SchemaUtils;

    const TABLE_NAME = 'activities';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->charset = 'utf8';
            $table->collation = env('APP_COLLATION', 'utf8_unicode_ci');
            $table->unsignedInteger('id', true)
                ->comment('Identifiant');
            $typeComment = <<<TEXT
Types de activite, ej:
0=>"unknown"
1=>"update"
2=>"status"
4=>"message"
8=>"client"
128=>"other"
TEXT;
            $table->unsignedTinyInteger('type')
                ->default(0)
                ->index()
                ->comment(self::setComment($typeComment));
            CreateUsersTable::foreignTo($table)
                ->comment("Identifiant de'utilizateur");
            $table->morphs('target');
            $table->timestamps();
            $table->softDeletes();
            $dataComment = <<<TEXT
Data changes, ej:
{
    "oroginal":{},
    "changement":{}
}
TEXT;
            $table->json('data')
                ->nullable()
                ->comment(self::setComment($dataComment));
            $table->longText('message')
                ->nullable()
                ->comment("Message");
            $table->engine = 'InnoDB';
        });
        self::setTableComment(self::TABLE_NAME, 'Activities');
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
