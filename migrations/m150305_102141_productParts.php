<?php

use yii\db\Schema;
use yii\db\Migration;

class m150305_102141_productParts extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        //  Справочник основных комплектаций.
        $this->createTable('{{%product_parts}}', [
            'id' => Schema::TYPE_PK,
            'product_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'name' => Schema::TYPE_STRING . ' NOT NULL',
            'description' => Schema::TYPE_TEXT . ' NOT NULL',
            'image' => Schema::TYPE_STRING . ' DEFAULT NULL',
            'parse_key' => Schema::TYPE_STRING . ' NOT NULL',
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%product_parts}}');
        return true;
    }
}
