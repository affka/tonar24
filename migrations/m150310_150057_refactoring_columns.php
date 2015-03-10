<?php

use yii\db\Schema;
use yii\db\Migration;

class m150310_150057_refactoring_columns extends Migration
{
    public function up()
    {
        $this->renameColumn('product_images', 'filename', 'hash');
        $this->renameColumn('product_compl_add', 'image', 'hash');
        $this->renameColumn('product_parts', 'image', 'hash');
        $this->renameColumn('product_files', 'filename', 'hash');

        $this->addColumn('product_images', 'ext', Schema::TYPE_STRING . ' DEFAULT NULL');
        $this->addColumn('product_compl_add', 'ext', Schema::TYPE_STRING . ' DEFAULT NULL');
        $this->addColumn('product_parts', 'ext', Schema::TYPE_STRING . ' DEFAULT NULL');
        $this->addColumn('product_files', 'ext', Schema::TYPE_STRING . ' DEFAULT NULL');
    }

    public function down()
    {
        $this->renameColumn('product_images', 'hash', 'filename');
        $this->renameColumn('product_compl_add', 'hash', 'image');
        $this->renameColumn('product_parts', 'hash', 'image');
        $this->renameColumn('product_files', 'hash', 'filename');

        $this->dropColumn('product_images', 'ext');
        $this->dropColumn('product_compl_add', 'ext');
        $this->dropColumn('product_parts', 'ext');
        $this->dropColumn('product_files', 'ext');
    }
}
