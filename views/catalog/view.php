<?php
/**
 * @var \app\models\Categories $model
 */

$this->params['breadcrumbs'][] = $model->name;
?>

<?= $model->description; ?>

<ul class="catalog-list">
    <?php /** @var \app\models\Products $item */ ?>
    <?php foreach ($model->items as $item) { ?>
        <li>
            <a href="/product/<?= $item->slug; ?>">
                <img src="<?= \helpers\ImageHelper::img($item->getFirstImage(), 200, 132); ?>" alt="#" />
                <div><?= $item->name ?></div>
                <div class="description"><?= $item->description_short ?></div>
            </a>
        </li>
    <?php } ?>
</ul>