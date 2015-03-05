<?php

use yii\db\Schema;
use yii\db\Migration;

class m150304_091606_costs extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        //  Справочник основных комплектаций.
        $this->createTable('{{%product_compl_main}}', [
            'id' => Schema::TYPE_PK,
            'product_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'model' => Schema::TYPE_STRING . ' DEFAULT NULL',
            'description' => Schema::TYPE_TEXT . ' DEFAULT NULL',
            'cost' => Schema::TYPE_STRING . ' DEFAULT NULL',
            'ccy' => Schema::TYPE_STRING . ' DEFAULT NULL'
        ], $tableOptions);

        //  Справочник доп. комплектаций.
        $this->createTable('{{%product_compl_add}}', [
            'id' => Schema::TYPE_PK,
            'product_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'name' => Schema::TYPE_TEXT . ' DEFAULT NULL',
            'image' => Schema::TYPE_STRING . ' DEFAULT NULL',
            'cost' => Schema::TYPE_STRING . ' DEFAULT NULL'
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%product_compl_main}}');
        $this->dropTable('{{%product_compl_add}}');

        return true;
    }
}
