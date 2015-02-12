<?php

use yii\db\Schema;
use yii\db\Migration;

class m150203_100739_first extends Migration
{
    /**
     * Миграция.
     */
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        //  Таблица категорий.
        $this->createTable('{{%categories}}', [
            'id' => Schema::TYPE_PK,
            'parent_id' => Schema::TYPE_INTEGER . ' DEFAULT NULL',
            'name' => Schema::TYPE_STRING . ' NOT NULL',
            'description' => Schema::TYPE_TEXT . ' DEFAULT NULL',
            'slug' => Schema::TYPE_STRING . ' NOT NULL'
        ], $tableOptions);

        $this->createIndex('IDX_categories_pid', '{{%categories}}', 'parent_id');

        //  Таблица товаров.
        $this->createTable('{{%products}}', [
            'id' => Schema::TYPE_PK,
            'category_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'name' => Schema::TYPE_STRING . ' NOT NULL',
            'slug' => Schema::TYPE_STRING . ' NOT NULL',
            'description' => Schema::TYPE_TEXT,
            'description_short' => Schema::TYPE_TEXT,
            'date_create' => Schema::TYPE_DATETIME,
            'date_update' => Schema::TYPE_DATETIME,
            'parse_key' => Schema::TYPE_STRING          //  Ключ для ассоциации товара с сайтом источником.
        ], $tableOptions);

        $this->createIndex('IDX_product_category', '{{%products}}', 'category_id');

        //  Таблица изображений.
        $this->createTable('{{%product_images}}', [
            'id' => Schema::TYPE_PK,
            'product_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'filename' => Schema::TYPE_STRING . ' NOT NULL'
        ], $tableOptions);

        $this->createIndex('IDX_product_id_images', '{{%product_images}}', 'product_id');

        //  Таблица свойств.
        $this->createTable('{{%product_properties}}', [
            'id' => Schema::TYPE_PK,
            'product_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'name' => Schema::TYPE_STRING . ' NOT NULL',
            'value' => Schema::TYPE_STRING . ' NOT NULL'
        ], $tableOptions);

        $this->createIndex('IDX_product_id_properties', '{{%product_properties}}', 'product_id');
    }

    /**
     * Откат изменений.
     */
    public function down()
    {
        $this->dropTable('{{%categories}}');
        $this->dropTable('{{%products}}');
        $this->dropTable('{{%product_images}}');
        $this->dropTable('{{%product_properties}}');
    }
}
