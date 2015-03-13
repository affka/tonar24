<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */
?>

<div class="container">
    <aside class="col-xs-3 catalog-menu">
        <h2>Каталог</h2>
        <?= \app\widgets\LeftMenu::widget(); ?>
    </aside>
    <section class="col-xs-9">
        <h1>Мы вам перезвоним!</h1>
        <br />

        <?php if (Yii::$app->session->hasFlash('contactFormSubmitted')): ?>

            <div class="alert alert-success">
                Спасибо, ваш запрос принят. В ближайшее время Вам перезвонят!
            </div>

        <?php else: ?>

            <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>
            <?= $form->field($model, 'phone') ?>
            <?= $form->field($model, 'body')->textArea(['rows' => 6]) ?>
            <div class="form-group">
                <div class="col-sm-offset-3 col-sm-6">
                    <?= Html::submitButton('Отправить', [
                        'class' => 'btn btn-primary',
                        'name' => 'contact-button'
                    ]) ?>
                </div>
            </div>
            <?php ActiveForm::end(); ?>

        <?php endif; ?>
    </section>
</div>

<?php $this->registerJs("$('#contactform-phone').focus()"); ?>