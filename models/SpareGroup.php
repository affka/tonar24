<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "spares_group".
 *
 * @property integer $id
 * @property string $title
 */
class SpareGroup extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'spares_group';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['title', 'required'],
            ['title', 'string', 'max' => 255],
        ];
    }

}
