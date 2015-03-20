<?php

namespace app\controllers;

use app\models\Axis;
use app\models\Categories;
use app\models\Decision;
use app\models\Spare;
use app\models\SpareGroup;
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

    public function actionSpares()
    {
        $sparesList = Spare::find()->where('ext <> ""')->all();
        $groups = SpareGroup::find()->all();

        $spares = [];
        foreach ($sparesList as $spare) {
            if (!isset($spares[$spare->group_id])) {
                $spares[$spare->group_id] = [];
            }
            $spares[$spare->group_id][] = $spare;
        }


        $this->getView()->title = 'Каталог запчастей';
        return $this->render('spares', [
            'spares' => $spares,
            'groups' => $groups,
        ]);
    }

    public function actionAxis()
    {
        $axis = Axis::find()->where('ext <> ""')->all();

        $this->getView()->title = 'Оси «Тонар»';
        return $this->render('axis', [
            'axis' => $axis,
        ]);
    }

    public function actionDecision($id)
    {
        $decision = Decision::findOne($id);
        if (!$decision) {
            throw new HttpException(404, 'Страница не найдена');
        }

        $this->getView()->title = 'Решение ' . $decision->name;
        return $this->render('decision', [
            'decision' => $decision,
        ]);
    }
}