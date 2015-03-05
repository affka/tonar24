<?php

use yii\db\Schema;
use yii\db\Migration;

class m150304_102210_service_map_init extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%dealers}}', [
            'id' => Schema::TYPE_PK,
            'tonarId' => Schema::TYPE_INTEGER . ' NOT NULL',
            'geoPointX' => Schema::TYPE_FLOAT,
            'geoPointY' => Schema::TYPE_FLOAT,
            'name' => Schema::TYPE_STRING . ' NOT NULL',
            'description' => Schema::TYPE_TEXT,
            'address' => Schema::TYPE_STRING,
            'phone' => Schema::TYPE_STRING,
            'siteUrl' => Schema::TYPE_STRING,
            'city' => Schema::TYPE_STRING
        ], $tableOptions);
        $this->createIndex('IDX_dealers_tonarId', '{{%dealers}}', 'tonarId');
    }

    public function down()
    {
        $this->dropTable('{{%dealers}}');
    }
}
