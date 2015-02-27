<?php

namespace app\controllers;

use app\models\Categories;
use app\models\Products;
use Yii;
use yii\web\Controller;
use yii\web\HttpException;

class ProductController extends Controller
{
    /**
     * Главная страница.
     */
    public function actionView($alias = '')
    {
        $this->getView()->title = 'Категория';

        $product = Products::findOne(['slug' => $alias]);
        if (!$product) {
            throw new HttpException(404, 'Страница не найдена');
        }

        return $this->render('view', [
            'product' => $product
        ]);
    }
}