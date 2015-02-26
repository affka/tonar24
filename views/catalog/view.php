<?php
/**
 * @var \app\models\Categories $model
 */

use app\components\ImageHelper;

$this->params['breadcrumbs'][] = $model->name;
?>

<?= $model->description; ?>

<ul class="catalog-list">
    <?php /** @var \app\models\Products $item */ ?>
    <?php foreach ($model->items as $item) { ?>
        <li>
            <a href="/product/<?= $item->slug; ?>">
                <?= ImageHelper::img($item->getFirstImage(), 200, 132); ?>
                <div><?= $item->name ?></div>
                <div class="description"><?= $item->description_short ?></div>
            </a>
        </li>
    <?php } ?>
</ul>