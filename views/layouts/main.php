<?php
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

/* @var $this \yii\web\View */
/* @var $content string */

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>

<?php $this->beginBody() ?>

<div class="container">
    <header>
    </header>
    <div class="row">
        <aside class="col-xs-4">
            <p>Каталог</p>
            <?= \app\widgets\LeftMenu::widget(); ?>
        </aside>
        <section class="col-xs-8">
            <?= Breadcrumbs::widget([
                'itemTemplate'=>"<li>{link} > </li>",
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                'homeLink' => [
                    'label' => 'Главная',
                    'url' => Yii::$app->getHomeUrl(),
                    'itemprop' => 'url',
                ],
            ]); ?>
            <?= $content ?>
        </section>
    </div>
</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
