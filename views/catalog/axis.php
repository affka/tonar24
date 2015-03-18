<?php
/**
     * @var \app\models\Axis[] $axis
 */

?>

<div class="container axis-container">
    <aside class="col-xs-3 catalog-menu">
        <h2>Каталог</h2>
        <?= \app\widgets\LeftMenu::widget(); ?>
    </aside>
    <section class="col-xs-9 row">
        <h1>Оси «Тонар»</h1>

        <?php /** @var \app\models\Axis $model */ ?>
        <?php foreach ($axis as $model) { ?>
            <div class="col-md-4">
                <div class="thumbnail">
                    <a href="<?= \app\components\ImageHelper::url($model, 1152, 864); ?>" data-lightbox="roadtrip<?= $model->id ?>">
                        <img src="<?= \app\components\ImageHelper::url($model, 242, 175, ['thumbnail' => true]); ?>" alt="<?= $model->title ?>" />
                    </a>
                    <div class="caption">
                        <p><?= $model->title ?></p>
                    </div>
                </div>
            </div>
        <?php } ?>
    </section>
</div>