<?php
/**
 * @var \app\models\Products $product
 * @var \app\models\Categories $product->category
 */

use app\components\ImageHelper;

$this->params['breadcrumbs'][] = $product->name;
?>



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
                        <div class="thumbnail">
                            <a href="<?= ImageHelper::url('/uploads/original/' . $image->filename, 1152, 864); ?>" data-lightbox="roadtrip">
                                <img src="<?= ImageHelper::url('/uploads/original/' . $image->filename, 200, 200, ['thumbnail' => true]); ?>" />
                            </a>
                        </div>
                    </div>
                <?php } ?>
            <?php } ?>
        </div>
    </div>
    <div class="col-xs-4">
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
                s
            </div>
        </div>
    </div>
</div>