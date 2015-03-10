<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "product_compl_add".
 *
 * @property integer $id
 * @property integer $product_id
 * @property string $name
 * @property string $image
 * @property string $cost
 */
class ProductComplAdd extends \yii\db\ActiveRecord
{
    /**
     * @var string
     */
    public $complImage = '';

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
            [['product_id'], 'required'],
            [['product_id'], 'integer'],
            [['name'], 'string'],
            [['image', 'cost'], 'string', 'max' => 255]
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
            'image' => 'Image',
            'cost' => 'Cost',
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        //  Загружаем новые изображения.
        if (!empty($this->complImage)) {
            $ext = strtolower(pathinfo($this->complImage, PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
                $this->image = md5(uniqid()) . 'cost.' . $ext;

                $uploadDir = Yii::$app->getBasePath() . '/web/uploads/original';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0775, true);
                }

                //  Качаем файл.
                $this->download($this->complImage, $uploadDir . '/' . $this->image);
            }
        }

        return parent::beforeSave($insert);
    }

    /**
     * @inheritdoc
     */
    public function afterDelete()
    {
        if (!empty($this->image)) {
            $uploadDir = Yii::$app->getBasePath() . '/web/uploads/original';
            if (is_file($uploadDir . $this->image)) {
                unlink($uploadDir . $this->image);
            }
        }

        parent::afterDelete();
    }

    /**
     * Сохранит файл на диск.
     * @param string $url
     * @param string $file
     */
    private function download($url, $file)
    {
        file_put_contents($file, file_get_contents($url));
    }
}
