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

<div id="panel"></div>
<div id="wrap">
    <!-- header -->
    <div class="header">
        <div class="logo">
            <a href="/"><img alt="Главная" title="Главная" src="/img/logo.png"/></a>

            <p class="slogan">
                &ldquo;Новая ступень качества&rdquo;
            </p>
            <a class="feedback" rel="nofollow" data-fancybox-type="iframe" href="#">Заказать обратный звонок</a>
            <a class="sales_department" rel="nofollow" data-fancybox-type="iframe"
                        href="#">Связаться с отделом продаж</a>
        </div>


        <div class="head-info" data=>
            <!--<noindex><a class="feedback" rel="nofollow" data-fancybox-type="iframe" href="/include/feedback.php">Заказать обратный звонок</a></noindex>
            <noindex><a class="sales_department" rel="nofollow" data-fancybox-type="iframe" href="/include/sales_department.php">Связаться с отделом продаж</a></noindex>-->


            <div class="head-info-telefons" data="">
                <p class="phone_not_moscow"></p>

                <p class="phone_region">8-800-700-35-95 </p>
            </div>
            <p><select name="select_region" class="select_region_style">
                    <option name="select_region" data_x="55.855944"
                            data_y="38.876336" value="1">
                        Центральный офис. Московская область
                    </option>
                    <option name="select_region" data_x="54.701413"
                            data_y="86.199481" value="99">
                        Филиал в Сибири
                    </option>
                    <option name="select_region" data_x="44.887140"
                            data_y="39.380677" value="114">
                        Филиал в Краснодарском крае
                    </option>
                    <option name="select_region" data_x="59.806982"
                            data_y="30.379342" value="104">
                        Филиал в Санкт-Петербурге
                    </option>
                    <option name="select_region" data_x="56.075426"
                            data_y="92.928498" value="142">
                        Филиал в Красноярске
                    </option>
                    <option name="select_region" data_x="51.288592"
                            data_y="37.561376" value="112">
                        Филиал в Центрально-Черноземном районе
                    </option>
                </select>
            </p>
            <!--<p class="phone">+7 (496) 416-32-49</p>
<p class="phone">+7 (800) 700-32-49</p>-->

        </div>
    </div>

    <div class="menu-clear-left"></div>


    <div class="clear"></div>

    <div class="container">
        <div class="aside">
            <div class="aside1">


                <div class="nav">
                    <p>Каталог</p>
                    <?= \app\widgets\LeftMenu::widget(); ?>
                </div>
            </div>
        </div>

        <div class="content">
            <?= $content ?>
        </div>


    </div>
</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
