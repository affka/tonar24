<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "dealers".
 *
 * @property integer $id
 * @property integer $tonarId
 * @property double $geoPointX
 * @property double $geoPointY
 * @property string $name
 * @property string $description
 * @property string $address
 * @property string $phone
 * @property string $siteUrl
 * @property string $city
 */
class Dealer extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'dealers';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tonarId', 'name'], 'required'],
            [['tonarId'], 'integer'],
            [['geoPointX', 'geoPointY'], 'number'],
            [['description'], 'string'],
            [['name', 'address', 'phone', 'siteUrl', 'city'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tonarId' => 'Tonar ID',
            'geoPointX' => 'Geo Point X',
            'geoPointY' => 'Geo Point Y',
            'name' => 'Name',
            'description' => 'Description',
            'address' => 'Address',
            'phone' => 'Phone',
            'siteUrl' => 'Site Url',
            'city' => 'City',
        ];
    }
}