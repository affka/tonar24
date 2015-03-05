<?php
/**
 * @var \app\models\Categories $model
 */

use app\components\ImageHelper;

$this->params['breadcrumbs'][] = $model->name;
?>

<div class="container">
    <aside class="col-xs-4 catalog-menu">
        <h2>Каталог</h2>
        <?= \app\widgets\LeftMenu::widget([
            'currentCategory' => $model,
        ]); ?>
    </aside>
    <section class="col-xs-8">
        <?= $model->description; ?>
        <div class="row">
            <?php /** @var \app\models\Products $item */ ?>
            <?php foreach ($model->items as $item) { ?>
                <div class="col-sm-6 col-md-4">
                    <div class="thumbnail thumbnail-product">
                        <a href="<?= \yii\helpers\Url::to(['/product/view', 'slug' => $item->slug]) ?>"><?= ImageHelper::img($item->getFirstImage(), 300, 132, ['thumbnail' => true]); ?></a>
                        <div class="caption">
                            <a href="<?= \yii\helpers\Url::to(['/product/view', 'slug' => $item->slug]) ?>"><?= $item->name ?></a>
                            <p><?= $item->description_short ?></p>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </section>
</div>