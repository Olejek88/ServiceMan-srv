<?php
/**
 * PHP Version 7.0
 *
 * @category Category
 * @package  Backend\controllers
 * @author   Максим Шумаков <ms.profile.d@gmail.com>
 * @license  http://www.yiiframework.com/license/ License name
 * @link     http://www.toirus.ru
 */

namespace backend\controllers;

use common\models\Event;
use common\models\Service;
use Yii;
use common\models\OrderStatus;
use common\models\Orders;
use common\models\Message;
use common\models\Users;

$query = Orders::find()
    ->select(
        '_id,
                uuid,
                title,
                orderStatusUuid,
                orderVerdictUuid,
                createdAt,
                changedAt,
                closeDate'
    );

$orderCountNew = $query->where('orderStatusUuid = :status',
    ['status' => OrderStatus::NEW_ORDER])->count();
$orderCountWork = $query->where('orderStatusUuid = :status',
    ['status' => OrderStatus::IN_WORK])->count();
$orderCountComplete = $query->where('orderStatusUuid = :status',
    ['status' => OrderStatus::COMPLETE])->count();
$orderCountUnComplete = $query->where('orderStatusUuid = :status',
    ['status' => OrderStatus::UN_COMPLETE])->count();
$orderCountCanceled = $query->where('orderStatusUuid = :status',
    ['status' => OrderStatus::CANCELED])->count();
Yii::$app->view->params['orderCountNew'] = $orderCountNew+$orderCountCanceled+$orderCountUnComplete;
Yii::$app->view->params['orderCountComplete'] = $orderCountComplete;
Yii::$app->view->params['orderCountWork'] = $orderCountWork;

$lastOrders = $query->orderBy("startDate DESC")
    ->asArray()
    ->limit(5)
    ->all();
Yii::$app->view->params['lastOrders'] = $lastOrders;

$accountUser = Yii::$app->user->identity;

$currentUser = Users::findOne(['userId' => $accountUser['id']]);
Yii::$app->view->params['currentUser'] = $currentUser;
$userImage = $currentUser->getImageUrl();
if (!$userImage)
    $userImage = Yii::$app->request->baseUrl.'/images/unknown2.png';

$messagesIncome = Message::find()
    ->where(['toUserUuid' => $currentUser['uuid']])
    ->andWhere(['status' => 0])
    ->orderBy('date DESC')
    ->all();

$messagesNewCount = Message::find()
    ->where(['toUserUuid' => $currentUser['uuid']])
    ->andWhere(['status' => 0])
    ->count();

$messagesChat = Message::find()
    ->asArray()
    ->all();

$today = date("Y-m-d H:i:s",time());
$today30 = date("Y-m-d H:i:s",time()+30*24*3600);

$events_near = Event::find()
    ->where(['<','next_date', $today30])
    ->orderBy('next_date')
    ->all();

$events_all = Event::find()
    ->orderBy('next_date')
    ->limit(5)
    ->all();

$events_warning = Event::find()
    ->where(['<','next_date', $today])
    ->orderBy('next_date')
    ->all();

Yii::$app->view->params['userImage'] = $userImage;

Yii::$app->view->params['messagesIncome'] = $messagesIncome;
Yii::$app->view->params['messagesNewCount'] = $messagesNewCount;
Yii::$app->view->params['messagesChat'] = $messagesChat;

Yii::$app->view->params['events_near'] = $events_near;
Yii::$app->view->params['events_all'] = $events_all;
Yii::$app->view->params['events_warning'] = $events_warning;

//$accountUser = Yii::$app->user->identity;
//Yii::$app->view->params['accountUser'] = $accountUser;

Yii::$app->view->params['service_stopped'] = Service::find()
    ->where(['=','status',0])
    ->count();
Yii::$app->view->params['service_running'] = Service::find()
    ->where(['=','status',1])
    ->count();

