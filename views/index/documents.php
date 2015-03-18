<?php
/**
     * @var \app\models\Document[] $documents
 */

?>

<div class="container documents-container">
    <aside class="col-xs-3 catalog-menu">
        <h2>Каталог</h2>
        <?= \app\widgets\LeftMenu::widget(); ?>
    </aside>
    <section class="col-xs-9 row">
        <h1>Нормативные документы</h1>

        <?php /** @var \app\models\Axis $model */ ?>
        <?php foreach ($documents as $model) { ?>
            <div class="media">
                <div class="media-left">
                    <a href="<?= $model->getUrl() ?>" target="_blank">
                        <img src="<?= $model->getIconUrl() ?>" width="32" height="32" alt="<?= $model->title ?>" />
                    </a>
                </div>
                <div class="media-body">
                    <table>
                        <tr>
                            <td>
                                <a href="<?= $model->getUrl() ?>" target="_blank">
                                    <?= $model->title ?>
                                </a>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        <?php } ?>
    </section>
</div>