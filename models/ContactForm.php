<?php

namespace app\models;

use Yii;
use yii\base\Model;

class ContactForm extends Model {

    public $phone;
    public $body;

    /**
     * @return array the validation rules.
     */
    public function rules() {
        return [
            // name, email, subject and body are required
            ['phone', 'required'],
            ['phone', 'match', 'pattern' => '/^[0-9 ()-+]+$/',
                'message' => 'Неверный формат телефона. Пример: +7 (912) 123-45-67'],
            ['body', 'string'],
        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            'phone' => 'Телефон',
            'body' => 'Комментарий',
        ];
    }

    /**
     * @return boolean
     */
    public function send() {
        if ($this->validate() && Yii::$app->params['adminEmail']) {
            Yii::$app->mailer->compose()
                ->setTo(Yii::$app->params['adminEmail'])
                ->setFrom(Yii::$app->params['fromEmail'])
                ->setSubject('Тонар - обратный звонок')
                ->setTextBody(
                    "Телефон: " . $this->phone . "\n" .
                    $this->body
                )
                ->send(Yii::$app->mailer);
            return true;
        }

        return false;
    }
}