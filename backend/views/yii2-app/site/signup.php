<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */

/* @var $model backend\models\SignupForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Sign In';

$fieldOptionsUser = [
    'options' => ['class' => 'form-group has-feedback'],
    'inputTemplate' => "{input}<span class='glyphicon glyphicon-user form-control-feedback'></span>"
];

$fieldOptionsLock = [
    'options' => ['class' => 'form-group has-feedback'],
    'inputTemplate' => "{input}<span class='glyphicon glyphicon-lock form-control-feedback'></span>"
];
?>

<div class="wrapper-block">
    <div class="panel panel-default wrapper-login-panel">
        <div class="panel-body">
            <div class="login-box">
                <div class="login-logo text-center">
                    <h4>
                        <a href="/" style=" color: #333; text-decoration: none;">
                            Сервис обслуживания
                        </a>
                    </h4>
                </div>

                <div class="site-signup">
                    <h1><?= Html::encode($this->title) ?></h1>

                    <p>Please fill out the following fields to signup:</p>

                    <div class="row">
                        <div class="col-lg-13">
                            <?php $form = ActiveForm::begin(['id' => 'form-signup']); ?>

                            <?= $form->field($model, 'username', $fieldOptionsUser)->textInput(['autofocus' => true]) ?>

                            <?= $form->field($model, 'email') ?>

                            <?= $form->field($model, 'password', $fieldOptionsLock)->passwordInput() ?>

                            <?= $form->field($model, 'password', $fieldOptionsLock)->passwordInput() ?>

                            <?= $form->field($model, 'organizationTitle')->textInput() ?>

                            <?= $form->field($model, 'organizationInn')->textInput() ?>

                            <div class="form-group">
                                <?= Html::submitButton('Signup', ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
                            </div>

                            <?php ActiveForm::end(); ?>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
