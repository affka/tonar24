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

    <header>
        <div class="container logo-line">
            <img src="<?= \Yii::$app->request->baseUrl ?>/img/logo.png" width="103" alt="<?= \Yii::$app->name ?>" />
            <div class="logo-name">
                «Тонар» Красноярск
                <div class="address">
                    ул. Северное шоссе 17
                </div>
            </div>
            <div class="logo-phone">
                Тел. +7 (391) 276-22-15
            </div>
        </div>

        <?php
            NavBar::begin([
            ]);
            echo Nav::widget([
                'options' => ['class' => 'navbar-nav'],
                'items' => [
                    ['label' => 'Главная', 'url' => ['/index/index']],
                    ['label' => 'Каталог', 'url' => ['/site/about']],
                ],
            ]);
            NavBar::end();
        ?>
    </header>
<div class="container">
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
