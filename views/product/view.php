<?php
/**
 * @var \app\models\Products $product
 * @var \app\models\Categories $product->category
 */

use app\components\ImageHelper;

$this->params['breadcrumbs'][] = $product->name;
?>

<div class="container">
    <aside class="col-xs-3 catalog-menu">
        <h2>Каталог</h2>
        <?= \app\widgets\LeftMenu::widget([
            'currentCategory' => $product->category,
        ]); ?>
    </aside>
    <section class="col-xs-9">
        <h2><?= $product->name ?></h2>
        <div class="row">
            <div class="col-xs-7">
                <div class="row product-thumbnail-main">
                    <a href="<?= ImageHelper::url('/uploads/original/' . $product->images[0]->filename, 1152, 864); ?>" data-lightbox="roadtrip">
                        <img src="<?= ImageHelper::url('/uploads/original/' . $product->images[0]->filename, 450, 450); ?>" />
                    </a>
                </div>
                <div class="row product-description">
                    <?= $product->description; ?>
                </div>
            </div>
            <div class="col-xs-5">
                <div class="row">
                    <?php foreach ($product->images as $key => $image) { ?>
                        <?php if ($key > 0) { ?>
                            <div class="product-thumbnail">
                                <a href="<?= ImageHelper::url('/uploads/original/' . $image->filename, 1152, 864); ?>" data-lightbox="roadtrip">
                                    <img src="<?= ImageHelper::url('/uploads/original/' . $image->filename, 100, 100, ['thumbnail' => true]); ?>" />
                                </a>
                            </div>
                        <?php } ?>
                    <?php } ?>
                </div>
                <div class="row">
                    <div class="panel panel-default product-links-panel">
                        <div class="panel-heading">Документы</div>
                        <div class="panel-body">
                            <ul>
                                <?php foreach ($product->files as $file) { ?>
                                    <li><a href="/uploads/files/<?= $file->filename ?>"><?= $file->name ?></a></li>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="row">
            <!-- Nav tabs -->
            <ul class="nav nav-tabs">
                <li class="active"><a href="#techTab" aria-controls="home" role="tab" data-toggle="tab">Технические характеристики</a></li>
                <li><a href="#costsTab" aria-controls="profile" role="tab" data-toggle="tab">Цены</a></li>
                <li><a href="#partsTab" aria-controls="profile" role="tab" data-toggle="tab">Детали</a></li>
            </ul>

            <!-- Tab panes -->
            <div class="tab-content">
                <div class="tab-pane active product-properties" id="techTab">
                    <table class="table">
                        <?php foreach ($product->properties as $property) { ?>
                            <tr>
                                <td><?= $property->name ?></td>
                                <td><?= $property->value ?></td>
                            </tr>
                        <?php } ?>
                    </table>
                </div>
                <div class="tab-pane product-price" id="costsTab">
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
                <div class="tab-pane product-details" id="partsTab">
                    <?php foreach ($product->parts as $model) { ?>
                    <div class="product-details-item">
                        <div class="product-details-image">
                            <a href="<?= ImageHelper::url('/uploads/original/' . $model->image, 1152, 864); ?>" data-lightbox="part<?= $model->id ?>">
                                <img src="<?= ImageHelper::url('/uploads/original/' . $model->image, 100, 100, ['thumbnail' => true]); ?>" />
                            </a>
                        </div>
                        <div class="product-details-description">
                            <strong><?= $model->name ?></strong><br/>
                            <p><?= $model->description ?></p>
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </section>
</div>