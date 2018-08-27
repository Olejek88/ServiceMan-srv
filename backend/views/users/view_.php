<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $model \common\models\Users */

$this->title = $model->name;
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'Пользователи'), 'url' => ['index']
];
?>
<div class="users-view box-padding">

    <div class="panel panel-default">
        <div class="panel-heading" style="background: #fff;">
            <h3 class="text-center" style="color: #333;">
                <?php echo Html::encode($this->title) ?>
            </h3>
        </div>
        <div class="panel-body">
            <div class="user-image-photo">
                <img src="<?php echo Html::encode($model->getImageUrl()) ?>" alt="">
            </div>
            <h1 class="text-center"></h1>

            <div id="myTabContent" class="tab-content">
                <div class="tab-pane fade active in" id="list">
                    <p class="text-center">
                        <?php
                        echo $this->render('@backend/views/yii2-app/layouts/buttons.php',
                            ['model' => $model]);
                        ?>
                    </p>
                    <h6>
                        <?php echo DetailView::widget(
                            [
                                'model' => $model,
                                'attributes' => [
                                    // [
                                    //     'label' => '№',
                                    //     'value' => $model->_id
                                    // ],
                                    // [
                                    //     'label' => 'Uuid',
                                    //     'value' => $model->uuid
                                    // ],
                                    [
                                        'label' => 'Имя',
                                        'value' => $model->name
                                    ],
                                    [
                                        'label' => 'Логин',
                                        'value' => $model->login
                                    ],
                                    [
                                        'label' => 'Пароль',
                                        'value' => $model->pass
                                    ],
                                    [
                                        'label' => 'Тип записи',
                                        'value' => $model->type
                                    ],
                                    [
                                        'label' => '№ метки',
                                        'value' => $model->tagId
                                    ],
                                    [
                                        'label' => 'Статус записи',
                                        'value' => $model->active
                                    ],
                                    [
                                        'label' => 'Должность',
                                        'value' => $model->whoIs
                                    ],
                                    [
                                        'label' => 'Идентификатор пользователя',
                                        'value' => $model->userId
                                    ],
                                    [
                                        'label' => 'Контакты',
                                        'value' => $model->contact
                                    ],
                                    [
                                        'label' => 'Дата создания',
                                        'value' => $model->createdAt
                                    ],
                                    [
                                        'label' => 'Дата изменения',
                                        'value' => $model->changedAt
                                    ],
                                ],
                            ]
                        ) ?>
                    </h6>
                </div>
            </div>
        </div>
    </div>
</div>
