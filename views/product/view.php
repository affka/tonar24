<?php
/**
 * @var \app\models\Products $product
 * @var \app\models\Categories $product->category
 */

use app\components\ImageHelper;

$this->params['breadcrumbs'][] = $product->name;
?>

<div class="container">
    <aside class="col-xs-4 catalog-menu">
        <h2>Каталог</h2>
        <?= \app\widgets\LeftMenu::widget([
            'currentCategory' => $product->category,
        ]); ?>
    </aside>
    <section class="col-xs-8">
        <div class="row">
            <div class="col-xs-8">
                <div class="row">
                    <?php foreach ($product->images as $key => $image) { ?>
                        <?php if ($key == 0) { ?>
                            <div class="col-xs-12">
                                <div class="thumbnail">
                                    <a href="<?= ImageHelper::url('/uploads/original/' . $image->filename, 1152, 864); ?>" data-lightbox="roadtrip">
                                        <img src="<?= ImageHelper::url('/uploads/original/' . $image->filename, 450, 450, ['thumbnail' => true]); ?>" />
                                    </a>
                                </div>
                            </div>
                        <?php } else { ?>
                            <div class="col-xs-4">
                                <div class="thumbnail thumbnail-product">
                                    <a href="<?= ImageHelper::url('/uploads/original/' . $image->filename, 1152, 864); ?>" data-lightbox="roadtrip">
                                        <img src="<?= ImageHelper::url('/uploads/original/' . $image->filename, 200, 75, ['thumbnail' => true]); ?>" />
                                    </a>
                                </div>
                            </div>
                        <?php } ?>
                    <?php } ?>
                </div>
            </div>
            <div class="col-xs-4">
                <div class="panel panel-default">
                    <div class="panel-heading">Скачать</div>
                    <div class="panel-body">
                        <ul>
                            <?php foreach ($product->files as $file) { ?>
                                <li><a href="/uploads/files/<?= $file->filename ?>"><?= $file->name ?></a></li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
                <?= $product->description; ?>
            </div>
            <div class="col-xs-12">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#techTab" aria-controls="home" role="tab" data-toggle="tab">Технические характеристики</a></li>
                    <li><a href="#costsTab" aria-controls="profile" role="tab" data-toggle="tab">Цены</a></li>
                </ul>

                <!-- Tab panes -->
                <div class="tab-content">
                    <div class="tab-pane active" id="techTab">
                        <table class="table">
                            <?php foreach ($product->properties as $property) { ?>
                                <tr>
                                    <td><?= $property->name ?></td>
                                    <td><?= $property->value ?></td>
                                </tr>
                            <?php } ?>
                        </table>
                    </div>
                    <div class="tab-pane" id="costsTab">
                        <h3>Основная комплектация</h3>
                        <table class="table">
                            <thead>
                            <tr>
                                <th>Модель</th>
                                <th>Описание</th>
                                <th>ССУ</th>
                                <th>Цена с НДС</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($product->complMain as $model) { ?>
                                <tr>
                                    <td><?= $model->model ?></td>
                                    <td><?= $model->description ?></td>
                                    <td><?= $model->ccy ?></td>
                                    <td><?= $model->cost ?></td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>

                        <?php if (count($product->complAdd)) { ?>
                            <h3>Дополнительные опции</h3>
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>Название</th>
                                    <th>Фото</th>
                                    <th>Цена</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($product->complAdd as $model) { ?>
                                    <tr>
                                        <td><?= $model->name ?></td>
                                        <td>
                                            <a href="<?= ImageHelper::url('/uploads/original/' . $model->image, 1152, 864); ?>" data-lightbox="roadtrip<?= $model->id ?>">
                                                <img src="<?= ImageHelper::url('/uploads/original/' . $model->image, 100, 100, ['thumbnail' => true]); ?>" />
                                            </a>
                                        <td><?= $model->cost ?></td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>