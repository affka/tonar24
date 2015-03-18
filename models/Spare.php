<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "spares".
 *
 * @property integer $id
 * @property integer $group_id
 * @property string $title
 * @property string $hash
 * @property string $ext
 */
class Spare extends \yii\db\ActiveRecord
{
    use FileTrait;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'spares';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['group_id', 'title'], 'required'],
            [['title', 'hash', 'ext'], 'string', 'max' => 255],
            [['hash', 'ext'], 'filter', 'filter' => 'strtolower'],
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
