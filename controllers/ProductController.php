<?php

namespace app\controllers;

use app\models\Categories;
use app\models\ProductLeaseForm;
use app\models\ProductOrderForm;
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
        $product = Products::findOne(['slug' => $slug]);
        if (!$product) {
            throw new HttpException(404, 'Страница не найдена');
        }

        $this->getView()->title = $product->name;
        return $this->render('view', [
            'product' => $product
        ]);
    }

    public function actionOrder($slug = '') {
        $product = Products::findOne(['slug' => $slug]);
        if (!$product) {
            throw new HttpException(404, 'Страница не найдена');
        }

        $this->getView()->title = 'Заказ — ' . $product->name;

        $model = new ProductOrderForm();
        $model->productId = $product->id;

        if ($model->load(Yii::$app->request->post()) && $model->send()) {
            Yii::$app->session->setFlash('contactFormSubmitted');
            return $this->refresh();
        } else {
            return $this->render('order', [
                'model' => $model,
                'product' => $product,
            ]);
        }
    }

    public function actionLeaseForm($slug = '') {
        $product = Products::findOne(['slug' => $slug]);
        if (!$product) {
            throw new HttpException(404, 'Страница не найдена');
        }

        $this->getView()->title = 'Заявка на лизинг — ' . $product->name;

        $model = new ProductLeaseForm();
        $model->productId = $product->id;

        if ($model->load(Yii::$app->request->post()) && $model->send()) {
            Yii::$app->session->setFlash('contactFormSubmitted');
            return $this->refresh();
        } else {
            return $this->render('lease', [
                'model' => $model,
                'product' => $product,
            ]);
        }
    }

}