<?php

use yii\db\Schema;
use yii\db\Migration;

class m150318_102820_spare_docs_axis_tables extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%spares}}', [
            'id' => Schema::TYPE_PK,
            'group_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'title' => Schema::TYPE_STRING . ' NOT NULL',
            'hash' => Schema::TYPE_STRING . ' NOT NULL',
            'ext' => Schema::TYPE_STRING,
        ], $tableOptions);

        $this->createTable('{{%spares_group}}', [
            'id' => Schema::TYPE_PK,
            'title' => Schema::TYPE_STRING . ' NOT NULL',
        ], $tableOptions);

        $this->createTable('{{%documents}}', [
            'id' => Schema::TYPE_PK,
            'title' => Schema::TYPE_STRING . ' NOT NULL',
            'hash' => Schema::TYPE_STRING . ' NOT NULL',
            'ext' => Schema::TYPE_STRING,
        ], $tableOptions);

        $this->createTable('{{%axis}}', [
            'id' => Schema::TYPE_PK,
            'title' => Schema::TYPE_STRING . ' NOT NULL',
            'hash' => Schema::TYPE_STRING . ' NOT NULL',
            'ext' => Schema::TYPE_STRING,
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%spares}}');
        $this->dropTable('{{%spares_group}}');
        $this->dropTable('{{%documents}}');
        $this->dropTable('{{%axis}}');
    }
}
