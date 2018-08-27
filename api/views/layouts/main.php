<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use frontend\assets\AppAsset;
use common\widgets\Alert;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body style="overflow-x: hidden;">
<?php $this->beginBody() ?>

<div class="wrap">
    <div class="first-block-header">
        <div class="row">
            <div class="col-md-4"></div>
            <div class="col-md-4">
                <div class="row text-center">
                    <div class="col-md-4"><a href="/">Виджеты</a></div>
                    <div class="col-md-4"><a href="https://github.com/mikaelwasp/yii.test">Документация</a></div>
                    <div class="col-md-4"><a href="/">Вход</a></div>
                </div>
                <div class="holst">
                    <div class="triangle-block-header-1"></div>
                    <div class="triangle-block-header-2"></div>
                    <div class="triangle-block-header-3"></div>
                    <div class="triangle-block-header-4"></div>
                    <div class="triangle-block-header-5"></div>
                    <div class="triangle-block-header-6"></div>
                </div>
            </div>
            <div class="col-md-4"></div>
        </div>
    </div>
    <div class="two-block-header">
        <div class="row">
            <div class="col-md-3"></div>
            <div class="col-md-6">
                <div class="row text-center box-block">
                    <div class="col-md-4 box-block-header">
                        <a href="/">
                            <div class="layout-block-header">
                                <i class="glyphicon glyphicon-asterisk" aria-hidden="true"></i>
                                <p>Виджеты</p>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4 box-block-header">
                        <a href="https://github.com/mikaelwasp/yii.test">
                            <div class="layout-block-header">
                            <i class="glyphicon glyphicon-book" aria-hidden="true"></i>
                            <p>Документация</p>
                        </div>
                        </a>
                    </div>
                    <div class="col-md-4 box-block-header">
                        <a href="https://github.com/mikaelwasp/yii.test">
                            <div class="layout-block-header">
                            <i class="glyphicon glyphicon-eye-open" aria-hidden="true"></i>
                            <p>Авторизация</p>
                        </div>
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-3"></div>
        </div>
    </div>
    <div class="container" style="padding: 0;">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
    <div class="last-block-footer">
        <div class="row">
            <div class="col-md-3"></div>
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-6 col-sm-6">
                        <div class="box-block-news">
                            <h3 class="text-center">Новости</h3>
                            <p>
                                23 января
                            </p>
                            <p>
                                Lorem ipsum dolor.
                            </p>
                            <p>
                                18 мая 2016
                            </p>
                            <p>
                                Lorem ipsum dolor sit amet, consectetur adipisicing elit. Iste, obcaecati.
                            </p>
                            <p>
                                25 августа 2015
                            </p>
                            <p>
                                Lorem ipsum dolor sit amet, consectetur.
                            </p>
                            <p>
                                23 июля 2015
                            </p>
                            <p>
                                Lorem ipsum dolor sit amet, consectetur adipisicing elit. Unde molestias pariatur incidunt! Molestias, sapiente.
                            </p>
                            <p>
                                23 октября 2014
                            </p>
                            <p>
                                Lorem ipsum dolor sit amet, consectetur adipisicing.
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-6">
                        <div class="box-block-info">
                            <a href="/">
                                <p>Общие сведения о Toir API</p>
                            </a>
                            <a href="/">
                                <p>Условия использования сервиса API</p>
                            </a>
                            <a href="/">
                                <p>Требования к названию приложения</p>
                            </a>
                            <a href="/">
                                <p>Использование логотипа toir.ru</p>
                            </a>
                            <h3>Регистрация приложения</h3>
                            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Voluptatem expedita accusantium accusamus quas obcaecati dolorem illo odio nemo laudantium, rerum nisi nostrum, veniam eaque perferendis itaque commodi quasi, enim nihil. Nostrum ipsam quia neque voluptate, sint rem dignissimos. Architecto voluptas illum, expedita sequi quidem ab, tenetur earum dicta deserunt harum commodi modi quas blanditiis est, dolorem nulla laborum vero ratione.</p>
                            <button type="button" class="btn btn-primary">Добавить приложение</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3"></div>
        </div>
    </div>
</div>

<footer class="footer block-footer">
    <div class="container">
        <p class="pull-left" style="color:#fff;">&copy; Toir API <?= date('Y') ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
