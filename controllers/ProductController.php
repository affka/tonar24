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
    public function actionView($slug = '')
    {
        $this->getView()->title = 'Категория';

        $product = Products::findOne(['slug' => $slug]);
        if (!$product) {
            throw new HttpException(404, 'Страница не найдена');
        }

        return $this->render('view', [
            'product' => $product
        ]);
    }
}