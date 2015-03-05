<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "product_files".
 *
 * @property integer $id
 * @property integer $product_id
 * @property string $filename
 */
class ProductFiles extends \yii\db\ActiveRecord
{
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
            [['product_id', 'filename', 'parse_key', 'name'], 'required'],
            [['product_id'], 'integer'],
            [['filename', 'parse_key', 'name'], 'string', 'max' => 255]
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
            'filename' => 'Filename',
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeValidate()
    {
        return parent::beforeValidate();
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        //  Загружаем новые изображения.
        $filename = md5(time()) . 'file.' . pathinfo($this->filename, PATHINFO_EXTENSION);

        $uploadDir = Yii::$app->getBasePath() . '/web/uploads/files';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0775, true);
        }

        //  Качаем файл.
        $this->filename = $this->download($this->filename, $uploadDir . '/' . $filename);

        return parent::beforeSave($insert);
    }

    /**
     * Сохранит файл на диск.
     * @param string $url
     * @param string $file
     */
    private function download($url, $file)
    {
        file_put_contents($file, file_get_contents($url));
        return basename($file);
    }
}
