<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\console\Exception;
use yii\db\ActiveRecord;
use yii\helpers\StringHelper;

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
 *
 * @property Categories $category
 */
class Products extends \yii\db\ActiveRecord
{
    /**
     * @var array
     */
    public $productImages = [];

    /**
     * @var array
     */
    public $productProperties = [];

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
     * Связь с таблицей product_images.
     * @return \yii\db\ActiveQuery
     */
    public function getImages()
    {
        return $this->hasMany(ProductImages::className(), ['product_id' => 'id']);
    }

    /**
     * Связь с таблицей categories.
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Categories::className(), ['id' => 'category_id']);
    }

    /**
     * Связь с таблицей product_properties.
     * @return \yii\db\ActiveQuery
     */
    public function getProperties()
    {
        return $this->hasMany(ProductProperties::className(), ['product_id' => 'id']);
    }

    /**
     * Связь с таблицей product_compl_main.
     * @return \yii\db\ActiveQuery
     */
    public function getComplMain()
    {
        return $this->hasMany(ProductComplMain::className(), ['product_id' => 'id']);
    }

    /**
     * Связь с таблицей product_compl_add.
     * @return \yii\db\ActiveQuery
     */
    public function getComplAdd()
    {
        return $this->hasMany(ProductComplAdd::className(), ['product_id' => 'id']);
    }

    /**
     * Связь с таблицей product_files.
     * @return \yii\db\ActiveQuery
     */
    public function getFiles()
    {
        return $this->hasMany(ProductFiles::className(), ['product_id' => 'id']);
    }

    /**
     * Связь с таблицей product_parts.
     * @return \yii\db\ActiveQuery
     */
    public function getParts()
    {
        return $this->hasMany(ProductParts::className(), ['product_id' => 'id']);
    }

    /**
     * Вернет модель первого изображения для этого товара.
     * @return string
     */
    public function getFirstImage()
    {
        $image = ProductImages::findOne(['product_id' => $this->id]);
        if (!$image) {
            return '';
        }

        return '/uploads/original/' . $image->filename;
    }

    /**
     * Сохранит изображения.
     */
    private function saveImages()
    {
        $this->productImages = array_unique($this->productImages);

        foreach ($this->productImages as $image) {
            $model = new ProductImages;
            $model->product_id = $this->id;
            $model->filename = md5($image) . '.' . strtolower(pathinfo($image, PATHINFO_EXTENSION));

            $uploadDir = Yii::$app->getBasePath() . '/web/uploads/original';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0775, true);
            }

            //  Качаем файл.
            $this->download($image, $uploadDir . '/' . $model->filename);

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
        foreach ($this->productProperties as $property) {
            $model = new ProductProperties;
            $model->product_id = $this->id;
            $model->name = $property['name'];
            $model->value = $property['value'];
            if ($model->validate() && !$model->save()) {
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
