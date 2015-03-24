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
                ул. Северное шоссе 25
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
                ['label' => 'Нормативные документы', 'url' => ['/index/documents']],
                ['label' => 'Контакты', 'url' => ['/index/contact']],
            ],
        ]);
    ?>
    <script>
        (function() {
            var cx = '006203079460066965725:7dgvqdi-9o8';
            var gcse = document.createElement('script');
            gcse.type = 'text/javascript';
            gcse.async = true;
            gcse.src = (document.location.protocol == 'https:' ? 'https:' : 'http:') +
            '//www.google.com/cse/cse.js?cx=' + cx;
            var s = document.getElementsByTagName('script')[0];
            s.parentNode.insertBefore(gcse, s);
        })();

        window.__gcse = window.__gcse || {};
        window.__gcse.callback = function() {
            $('form.gsc-search-box')
                .on('keyup', 'input.gsc-input', function (e) {
                    if (e.keyCode === 13) {
                        $('#content').hide();
                    }
                })
                .on('click', 'input.gsc-search-button', function (e) {
                    $('#content').hide();
                });
        }
    </script>
    <gcse:searchbox></gcse:searchbox>
    <?php
        NavBar::end();
    ?>
</header>

<div class="container">
    <gcse:searchresults></gcse:searchresults>
</div>
<div id="content">
    <?= $content ?>
</div>

<?php if (Yii::$app->params['enableGoogleAnalytics']) { ?>
    <script>
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

        ga('create', 'UA-60914589-1', 'auto');
        ga('send', 'pageview');
    </script>
<?php } ?>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
