<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "product_images".
 *
 * @property integer $id
 * @property integer $product_id
 * @property string $hash
 * @property string $ext
 */
class ProductImages extends \yii\db\ActiveRecord
{
    use FileTrait;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'product_images';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['product_id'], 'required'],
            [['remoteUrl'], 'required', 'on' => 'insert'],
            [['product_id'], 'integer'],
            [['hash', 'ext'], 'string', 'max' => 255],
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
