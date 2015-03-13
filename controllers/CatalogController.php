<?php

namespace app\controllers;

use app\models\Categories;
use Yii;
use yii\web\Controller;
use yii\web\HttpException;

class CatalogController extends Controller
{
    public function actionView($slug = '')
    {
        $category = Categories::findOne(['slug' => $slug]);
        if (!$category) {
            throw new HttpException(404, 'Категория не найдена');
        }

        $this->getView()->title = $category->name;
        return $this->render('view', [
            'model' => $category
        ]);
    }
}