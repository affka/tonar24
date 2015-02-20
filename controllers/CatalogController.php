<?php

namespace app\controllers;

use app\models\Categories;
use Yii;
use yii\web\Controller;
use yii\web\HttpException;

class CatalogController extends Controller
{
    /**
     * Главная страница.
     */
    public function actionView($alias = '')
    {
        $this->getView()->title = 'Категория';

        $category = Categories::findOne(['slug' => $alias]);
        if (!$category) {
            throw new HttpException(404, 'Категория не найдена');
        }

        return $this->render('view', [
            'model' => $category
        ]);
    }
}