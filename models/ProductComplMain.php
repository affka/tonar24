<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "product_compl_main".
 *
 * @property integer $id
 * @property integer $product_id
 * @property string $model
 * @property string $description
 * @property string $cost
 * @property string $ccy
 */
class ProductComplMain extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'product_compl_main';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['product_id'], 'required'],
            [['product_id'], 'integer'],
            [['description'], 'string'],
            [['model', 'cost', 'ccy'], 'string', 'max' => 255]
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
            'model' => 'Model',
            'description' => 'Description',
            'cost' => 'Cost',
            'ccy' => 'Ccy',
        ];
    }
}
