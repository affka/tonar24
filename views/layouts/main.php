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
    <link rel="shortcut icon" href="<?= \Yii::$app->request->baseUrl ?>/favicon.ico" />
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?> — <?= Yii::$app->name ?></title>
    <?php $this->head() ?>
</head>
<body>

<?php $this->beginBody() ?>

<header>
    <div class="container logo-line">
        <a href="<?= \yii\helpers\Url::to(['/index/index']); ?>">
            <img src="<?= \Yii::$app->request->baseUrl ?>/img/logo.png" width="103" alt="<?= \Yii::$app->name ?>" />
        </a>
        <div class="logo-name">
            <a href="<?= \yii\helpers\Url::to(['/index/index']); ?>">
            ООО «Красноярск Тонар Сервис»
            </a>
            <div class="address">
                ул. Северное шоссе 17
            </div>
        </div>
        <div class="logo-phone">
            Тел. +7 (391) 276-22-15
            <a href="<?= \yii\helpers\Url::to(['/index/call-back']); ?>">Заказать обратный звонок</a>
        </div>
    </div>

    <?php
        NavBar::begin([
        ]);
        echo Nav::widget([
            'options' => ['class' => 'navbar-nav'],
            'items' => [
                ['label' => 'Главная', 'url' => ['/index/index']],
                ['label' => 'Карта сервисной сети', 'url' => ['/index/service-map']],
                ['label' => 'Контакты', 'url' => ['/index/contact']],
            ],
        ]);
        NavBar::end();
    ?>
</header>

<?= $content ?>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
