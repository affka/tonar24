<?php
/**
 * @var \app\models\Categories $model
 */

use app\components\ImageHelper;

$this->params['breadcrumbs'][] = $model->name;
?>

<?= $model->description; ?>

<div class="row">
    <?php /** @var \app\models\Products $item */ ?>
    <?php foreach ($model->items as $item) { ?>
        <div class="col-sm-6 col-md-4">
            <div class="thumbnail thumbnail-product">
                <a href="<?= \yii\helpers\Url::to(['/product/view', 'slug' => $item->slug]) ?>" class="catalog-img"><?= ImageHelper::img($item->getFirstImage(), 300, 132, ['thumbnail' => true]); ?></a>
                <div class="caption">
                    <a href="<?= \yii\helpers\Url::to(['/product/view', 'slug' => $item->slug]) ?>"><?= $item->name ?></a>
                    <p><?= $item->description_short ?></p>
                </div>
            </div>
        </div>
    <?php } ?>
</div>