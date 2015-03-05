<?php

use yii\db\Schema;
use yii\db\Migration;

class m150305_082957_downloadLinks extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        //  Справочник основных комплектаций.
        $this->createTable('{{%product_files}}', [
            'id' => Schema::TYPE_PK,
            'product_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'filename' => Schema::TYPE_STRING . ' NOT NULL',
            'name' => Schema::TYPE_STRING . ' NOT NULL',
            'parse_key' => Schema::TYPE_STRING . ' NOT NULL',
        ], $tableOptions);

        $filesDir = Yii::$app->basePath . '/web/uploads/files';
        if (!is_dir($filesDir)) {
            mkdir($filesDir);
        }
    }

    public function down()
    {
        $this->dropTable('{{%product_files}}');
        return true;
    }
}
