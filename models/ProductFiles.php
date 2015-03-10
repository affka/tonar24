<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "product_files".
 *
 * @property integer $id
 * @property integer $product_id
 * @property string $hash
 * @property string $ext
 */
class ProductFiles extends \yii\db\ActiveRecord
{
    use FileTrait;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'product_files';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['product_id', 'hash', 'name'], 'required'],
            [['product_id'], 'integer'],
            [['hash', 'ext', 'name'], 'string', 'max' => 255]
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
