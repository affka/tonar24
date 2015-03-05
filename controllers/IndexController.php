<?php

namespace app\controllers;

use app\models\Dealer;
use Yii;
use yii\web\Controller;

class IndexController extends Controller
{
    /**
     * Главная страница.
     */
    public function actionIndex()
    {
        $this->getView()->title = 'Главная страница';
        return $this->render('index');
    }

    /**
     * Отображение ошибки на сайте.
     */
    public function actionContact()
    {
        $this->getView()->title = 'Контактная информация';
        return $this->render('contact');
    }

    /**
     * Отображение ошибки на сайте.
     */
    public function actionServiceMap()
    {
        return $this->render('service-map', [
            'dealerModels' => Dealer::find()->all(),
        ]);
    }

    /**
     * Отображение ошибки на сайте.
     */
    public function actionError()
    {
        //
    }
}