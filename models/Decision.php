<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "decisions".
 *
 * @property integer $id
 * @property string $name
 * @property Products[] $products
 */
class Decision extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'decisions';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['name', 'required'],
            ['name', 'string', 'max' => 255],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProducts()
    {
        return $this->hasMany(Products::className(), ['id' => 'decision_id'])
            ->viaTable('decisions_products_junction', ['product_id' => 'id']);
    }

}
