<?php
/**
 * @var array $spares
 * @var \app\models\SpareGroup[] $groups
 */

?>

<div class="container spares-container">
    <aside class="col-xs-3 catalog-menu">
        <h2>Каталог</h2>
        <?= \app\widgets\LeftMenu::widget(); ?>
    </aside>
    <section class="col-xs-9 row">
        <h1>Каталог запчастей</h1>

        <?php foreach ($groups as $group) {
            if (!isset($spares[$group->id])) {
                continue;
            }
            ?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><?= $group->title ?></h3>
                </div>
                <div class="panel-body">

                    <?php /** @var \app\models\Spare $model */ ?>
                    <?php foreach ($spares[$group->id] as $model) { ?>
                        <div class="media">
                            <div class="media-left">
                                <a href="<?= $model->getUrl() ?>" target="_blank">
                                    <img src="<?= $model->getIconUrl() ?>" width="32" height="32" alt="<?= $model->title ?>" />
                                </a>
                            </div>
                            <div class="media-body">
                                <a href="<?= $model->getUrl() ?>" target="_blank"><?= $model->title ?></a>
                            </div>
                        </div>
                    <?php } ?>

                </div>
            </div>

        <?php } ?>
    </section>
</div>