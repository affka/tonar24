<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\helpers\Url;

class ProductOrderForm extends Model {

    public $productId;
    public $phone;
    public $email;
    public $count = 1;
    public $companyName;
    public $userName;
    public $body;

    /**
     * @return array the validation rules.
     */
    public function rules() {
        return [
            [['productId', 'phone'], 'required'],
            [['body', 'companyName', 'userName'], 'string'],
            ['email', 'email'],
            ['count', 'number', 'min' => 1],
            ['phone', 'match', 'pattern' => '/^[0-9 ()-+]+$/',
                'message' => 'Неверный формат телефона. Пример: +7 (912) 123-45-67'],
        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            'phone' => 'Телефон',
            'email' => 'Email',
            'count' => 'Количество',
            'companyName' => 'Организация',
            'userName' => 'ФИО',
            'body' => 'Комментарий',
        ];
    }

    /**
     * @return boolean
     */
    public function send() {
        if ($this->validate() && Yii::$app->params['adminEmail']) {
            $product = $this->getProduct();
            Yii::$app->mailer->compose()
                ->setTo(Yii::$app->params['adminEmail'])
                ->setFrom(Yii::$app->params['fromEmail'])
                ->setSubject('Тонар - заказ товара')
                ->setTextBody(
                    "Товар `" . $product->name . "` - " . Url::to(['/product/view', 'slug' => $product->slug], true) . "\n" .
                    "Телефон: " . $this->phone . "\n" .
                    "Email: " . $this->email . "\n" .
                    "Количество: " . $this->count . "\n" .
                    "Организация: " . $this->companyName . "\n" .
                    "ФИО: " . $this->userName . "\n" .
                    $this->body
                )
                ->send(Yii::$app->mailer);
            return true;
        }

        return false;
    }

    /**
     * @return Products
     */
    public function getProduct()
    {
        return Products::findOne($this->productId);
    }
}