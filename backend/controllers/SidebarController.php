<?php
namespace backend\controllers;

use common\models\Event;
use common\models\Service;
use Yii;
use common\models\OrderStatus;
use common\models\Orders;
use common\models\Message;
use common\models\Users;

$accountUser = Yii::$app->user->identity;

$currentUser = Users::findOne(['user_id' => $accountUser['id']]);
Yii::$app->view->params['currentUser'] = $currentUser;
$userImage = Yii::$app->request->baseUrl.'/images/unknown2.png';

Yii::$app->view->params['userImage'] = $userImage;

