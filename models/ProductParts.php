<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "product_parts".
 *
 * @property integer $id
 * @property string $name
 * @property string $description
 * @property string $hash
 * @property string $ext
 * @property string $parse_key
 */
class ProductParts extends \yii\db\ActiveRecord
{
    use FileTrait;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'product_parts';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['parse_key'], 'required'],
            [['description'], 'string'],
            [['name', 'hash', 'ext', 'parse_key'], 'string', 'max' => 255],
            [['hash', 'ext'], 'filter', 'filter' => 'strtolower'],
        ];
    }

    public function beforeSave($insert) {
        if ($insert) {
            $this->saveFiles();
        }

        return parent::beforeSave($insert);
    }

    public function afterDelete() {
        parent::afterDelete();

        $this->removeFiles();
    }
}
