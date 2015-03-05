<?php

use yii\db\Schema;
use yii\db\Migration;

class m150305_060427_parseImageKey extends Migration
{
    public function up()
    {
        $this->addColumn('product_images', 'parse_key', Schema::TYPE_STRING . ' NOT NULL');
    }

    public function down()
    {
        $this->dropColumn('product_images', 'parse_key');
        return true;
    }
}
