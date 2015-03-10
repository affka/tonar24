<?php

use yii\db\Schema;
use yii\db\Migration;

class m150310_112124_productPartsManyMany extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%product_parts_junction}}', [
            'product_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'part_id' => Schema::TYPE_INTEGER . ' NOT NULL'
        ], $tableOptions);
        $this->dropColumn('product_parts', 'product_id');
    }

    public function down()
    {
        $this->addColumn('product_parts', 'product_id', Schema::TYPE_INTEGER . ' NOT NULL');
        $this->dropTable('{{%product_parts_junction}}');
    }
}
