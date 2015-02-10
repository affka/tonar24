<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\console\Exception;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "products".
 *
 * @property integer $id
 * @property integer $category_id
 * @property string $name
 * @property string $description
 * @property string $description_short
 * @property string $date_create
 * @property string $date_update
 * @property string $parse_key
 */
class Products extends \yii\db\ActiveRecord
{
    /**
     * @var array
     */
    public $images = [];

    /**
     * @var array
     */
    public $properties = [];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'products';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'slug' => [
                'class' => 'Zelenin\yii\behaviors\Slug',
                'slugAttribute' => 'slug',
                'attribute' => 'name',
                // optional params
                'ensureUnique' => true,
                'translit' => true,
                'replacement' => '-',
                'lowercase' => true,
                'immutable' => false,
                // If intl extension is enabled, see http://userguide.icu-project.org/transforms/general.
                'transliterateOptions' => 'Russian-Latin/BGN;'
            ],
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'date_create',
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'date_update',
                ],
                'value' => function () {
                    return date('U'); // unix timestamp
                },
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['category_id', 'name', 'slug'], 'required'],
            [['category_id'], 'integer'],
            [['description', 'description_short'], 'string'],
            [['date_create', 'date_update'], 'safe'],
            [['name', 'parse_key', 'slug'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'category_id' => 'Category ID',
            'name' => 'Name',
            'description' => 'Description',
            'description_short' => 'Description Short',
            'date_create' => 'Date Create',
            'date_update' => 'Date Update',
            'parse_key' => 'Parse Key',
        ];
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        $this->saveImages();
        $this->saveProperties();

        parent::afterSave($insert, $changedAttributes);
    }

    /**
     * Соранит изображения.
     */
    private function saveImages()
    {
        if (!count($this->images)) {
            return;
        }

        foreach ($this->images as $image) {
            $model = new ProductImages;
            $model->product_id = $this->id;
            $model->filename = md5(time()) . '.' . end(explode('.', $image));

            //  Качаем файл.
            $this->download($image, Yii::$app->getBasePath() . '/web/uploads/' . $model->filename);

            if (!$model->save()) {
                throw new Exception('Не удалось сохранить изображение для товара ' . $this->id);
            }
        }
    }

    /**
     * Сохранит свойства.
     */
    private function saveProperties()
    {
        if (!count($this->properties)) {
            return;
        }

        foreach ($this->properties as $property) {
            $model = new ProductProperties;
            $model->product_id = $this->id;
            $model->name = $property['name'];
            $model->value = $property['value'];
            if (!$model->save()) {
                var_dump($model->getErrors());
                throw new Exception('Не удалось сохранить свойство для товара ' . $this->id);
            }
        }
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
