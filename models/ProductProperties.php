<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "product_properties".
 *
 * @property integer $id
 * @property integer $product_id
 * @property string $name
 * @property string $value
 */
class ProductProperties extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'product_properties';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['product_id', 'name', 'value'], 'required'],
            [['product_id'], 'integer'],
            [['name', 'value'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'product_id' => 'Product ID',
            'name' => 'Name',
            'value' => 'Value',
        ];
    }
}
