<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "product_compl_add".
 *
 * @property integer $id
 * @property integer $product_id
 * @property string $name
 * @property string $hash
 * @property string $ext
 * @property string $cost
 */
class ProductComplAdd extends \yii\db\ActiveRecord
{
    use FileTrait;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'product_compl_add';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['product_id', 'required'],
            ['remoteUrl', 'required', 'on' => 'insert'],
            [['product_id'], 'integer'],
            [['name'], 'string'],
            [['hash', 'cost', 'ext'], 'string', 'max' => 255],
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
