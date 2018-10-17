<?php

namespace backend\controllers;

use common\models\OrderStatus;
use common\models\Users;
use Yii;

$accountUser = Yii::$app->user->identity;

$currentUser = Users::findOne(['user_id' => $accountUser['id']]);
Yii::$app->view->params['currentUser'] = $currentUser;
$userImage = Yii::$app->request->baseUrl . '/images/unknown2.png';

Yii::$app->view->params['userImage'] = $userImage;

