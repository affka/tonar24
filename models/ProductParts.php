<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "product_parts".
 *
 * @property integer $id
 * @property string $name
 * @property string $description
 * @property string $image
 * @property string $parse_key
 */
class ProductParts extends \yii\db\ActiveRecord
{
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
            [['name', 'image', 'parse_key'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'description' => 'Description',
            'image' => 'Image',
            'parse_key' => 'Parse Key',
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        //  Загружаем новые изображения.
        $filename = md5(uniqid()) . 'part.' . strtolower(pathinfo($this->image, PATHINFO_EXTENSION));

        $uploadDir = Yii::$app->getBasePath() . '/web/uploads/original';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0775, true);
        }

        //  Качаем файл.
        $this->image = $this->download($this->image, $uploadDir . '/' . $filename);

        return parent::beforeSave($insert);
    }

    /**
     * Сохранит файл на диск.
     * @param string $url
     * @param string $file
     */
    private function download($url, $file)
    {
        if (!$file || !$url) {
            return '';
        }

        file_put_contents($file, file_get_contents($url));
        return basename($file);
    }
}
