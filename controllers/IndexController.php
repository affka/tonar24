<?php

namespace app\controllers;

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
    public function actionError()
    {
        //
    }
}