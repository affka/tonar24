<?php
/**
     * @var \app\models\Decision $decision
 */

?>

<div class="container">
    <aside class="col-xs-3 catalog-menu">
        <h2>Каталог</h2>
        <?= \app\widgets\LeftMenu::widget(); ?>
    </aside>
    <section class="col-xs-9">
        <h1><?= $decision->name ?></h1>
        <div class="row">
            <?php foreach ($decision->products as $item) { ?>
                <div class="col-sm-6 col-md-4">
                    <div class="thumbnail-catalog">
                        <a href="<?= \yii\helpers\Url::to(['/product/view', 'slug' => $item->slug]) ?>" class="catalog-img"><?= \app\components\ImageHelper::img($item->getFirstImage(), 300, 200, ['thumbnail' => true]); ?></a>
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