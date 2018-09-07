<?php
/**
 * PHP Version 7.0
 *
 * @category Category
 * @package  Api\controllers
 * @author   Максим Шумаков <ms.profile.d@gmail.com>
 * @license  http://www.yiiframework.com/license/ License name
 * @link     http://www.toirus.ru
 */
namespace api\old\controllers;

use common\components\MyHelpers;
use common\models\Operation;
use common\models\OrderStatus;
use common\models\Task;
use common\models\Stage;
use Yii;
use yii\rest\ActiveController;
use yii\filters\ContentNegotiator;
use yii\web\NotAcceptableHttpException;
use yii\web\UnauthorizedHttpException;
use yii\web\Response;
use common\models\Orders;

/**
 * Класс работы с нарядами.
 *
 * @category Category
 * @package  Api\controllers
 * @author   Максим Шумаков <ms.profile.d@gmail.com>
 * @license  http://www.yiiframework.com/license/ License name
 * @link     http://www.toirus.ru
 */
class OrdersController extends ActiveController
{
    public $modelClass = 'common\models\Orders';
    public $enableCsrfValidation = false;

    /**
     * Behaviors.
     *
     * @return array
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['contentNegotiator'] = [
            'class' => ContentNegotiator::className(),
            'formats' => [
                'application/json' => Response::FORMAT_JSON
            ]

        ];
        return $behaviors;
    }

    /**
     * Инициализация.
     *
     * @return void
     * @throws UnauthorizedHttpException
     */
    public function init()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $token = TokenController::getTokenString(Yii::$app->request);
        // проверяем авторизацию пользователя
        if (!TokenController::isTokenValid($token)) {
            throw new UnauthorizedHttpException();
        }
    }

    /**
     * Actions.
     *
     * @return array
     */
    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        return $actions;
    }

    /**
     * Index.
     *
     * @return Orders[]
     * @throws UnauthorizedHttpException
     */
    public function actionIndex()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        // по токену находим пользователя которому принадлежат наряды
        $token = TokenController::getTokenString(Yii::$app->request);
        $user = TokenController::getUserByToken($token);
        if ($user == null) {
            throw new UnauthorizedHttpException();
        }

        // проверяем параметры запроса
        $req = Yii::$app->request;
        $query = Orders::find();
        $query->andWhere(['userUuid' => $user->uuid]);

        $id = $req->getQueryParam('id');
        if ($id != null) {
            $query->andWhere(['_id' => $id]);
        }

        $uuid = $req->getQueryParam('uuid');
        if ($uuid != null) {
            $query->andWhere(['uuid' => $uuid]);
        }

        $changedAfter = $req->getQueryParam('changedAfter');
        if ($changedAfter != null) {
            $query->andWhere(['>=', 'changedAt', $changedAfter]);
        }

        $status = $req->getQueryParam('status');
        if (!is_array($status)) {
            if (OrdersController::checkStatus($status)) {
                $query->andWhere(['orderStatusUuid' => $status]);
            }
        } else {
            $resArray = array();
            foreach ($status as $value) {
                if (OrdersController::checkStatus($value)) {
                    $resArray[] = $value;
                }
            }

            $query->andWhere(['orderStatusUuid' => $resArray]);
        }

        // проверяем что хоть какие-то условия были заданы
        if ($query->where == null) {
            return [];
        }

        // выбираем данные из базы
        $result = $query->all();
        return $result;
    }

    /**
     * Метод для приёма от клиента результата выполнения наряда.
     *
     * @return array
     */
    public function actionResults()
    {
        $success = true;
        $saved = array();

        $request = Yii::$app->request;
        if ($request->isPost) {
            $params = $request->bodyParams;
            foreach ($params as $order) {
                foreach ($order['tasks'] as $task) {
                    foreach ($task['stages'] as $stage) {
                        foreach ($stage['operations'] as $op) {
                            $model = Operation::findOne(
                                ['_id' => $op['_id']]
                            );
                            $model->attributes = $op;

                            $model->setAttribute(
                                'operationStatusUuid',
                                $op['operationStatus']['uuid']
                            );
                            $model->setAttribute(
                                'operationVerdictUuid',
                                $op['operationVerdict']['uuid']
                            );
                            $model->setAttribute(
                                'startDate',
                                MyHelpers::parseFormatDate($op['startDate'])
                            );
                            $model->setAttribute(
                                'endDate',
                                MyHelpers::parseFormatDate($op['endDate'])
                            );
                            $model->setAttribute(
                                'createdAt',
                                MyHelpers::parseFormatDate($op['createdAt'])
                            );
                            $model->setAttribute(
                                'changedAt',
                                MyHelpers::parseFormatDate($op['changedAt'])
                            );

                            // TODO: нужно как-то сообщить что произошла ошибка
                            // возможно нужно строить массив объектов которые
                            // в транзакции сохранять, если ошибка, откатываем
                            // и собщаем о ошибке
                            $model->save();
                        }

                        $model = Stage::findOne(['_id' => $stage['_id']]);
                        $model->attributes = $stage;

                        $model->setAttribute(
                            'stageStatusUuid',
                            $stage['stageStatus']['uuid']
                        );
                        $model->setAttribute(
                            'stageVerdictUuid',
                            $stage['stageVerdict']['uuid']
                        );
                        $model->setAttribute(
                            'startDate',
                            MyHelpers::parseFormatDate($stage['startDate'])
                        );
                        $model->setAttribute(
                            'endDate',
                            MyHelpers::parseFormatDate($stage['endDate'])
                        );
                        $model->setAttribute(
                            'createdAt',
                            MyHelpers::parseFormatDate($stage['createdAt'])
                        );
                        $model->setAttribute(
                            'changedAt',
                            MyHelpers::parseFormatDate($stage['changedAt'])
                        );

                        $model->save();
                    }

                    $model = Task::findOne(['_id' => $task['_id']]);
                    $model->attributes = $task;

                    $model->setAttribute(
                        'taskStatusUuid', $task['taskStatus']['uuid']
                    );
                    $model->setAttribute(
                        'taskVerdictUuid', $task['taskVerdict']['uuid']
                    );
                    $model->setAttribute(
                        'startDate',
                        MyHelpers::parseFormatDate($task['startDate'])
                    );
                    $model->setAttribute(
                        'endDate',
                        MyHelpers::parseFormatDate($task['endDate'])
                    );
                    $model->setAttribute(
                        'createdAt',
                        MyHelpers::parseFormatDate($task['createdAt'])
                    );
                    $model->setAttribute(
                        'changedAt',
                        MyHelpers::parseFormatDate($task['changedAt'])
                    );

                    $model->save();
                }

                $model = Orders::findOne(['_id' => $order['_id']]);
                $model->attributes = $order;

                $model->setOrderStatusUuid($order['orderStatus']['uuid']);
                $model->setOrderVerdictUuid($order['orderVerdict']['uuid']);
                $model->setOrderLevelUuid($order['orderLevel']['uuid']);
                $model->setAttemptSendDate('0000-00-00 00:00:00');
                $model->setAttemptCount($order['attemptCount']);
                $model->setUpdated($order['updated']);
                $model->setAttribute(
                    'receivDate',
                    MyHelpers::parseFormatDate($order['receivDate'])
                );
                $model->setAttribute(
                    'startDate',
                    MyHelpers::parseFormatDate($order['startDate'])
                );
                $model->setAttribute(
                    'openDate',
                    MyHelpers::parseFormatDate($order['openDate'])
                );
                $model->setAttribute(
                    'closeDate',
                    MyHelpers::parseFormatDate($order['closeDate'])
                );
                $model->setAttribute(
                    'createdAt',
                    MyHelpers::parseFormatDate($order['createdAt'])
                );
                $model->setAttribute(
                    'changedAt',
                    MyHelpers::parseFormatDate($order['changedAt'])
                );

                if ($model->save()) {
                    $saved[] = [
                        '_id' => $model['_id'],
                        'uuid' => $model['uuid']
                    ];
                } else {
                    $success = false;
                }
            }

            // результат по факту возвращается только для нарядов
            return ['success' => $success, 'data' => $saved];
        } else {
            return [];
        }
    }

    /**
     * Установка статуса наряда - В работе.
     *
     * @return array
     * @throws NotAcceptableHttpException
     */
    public function actionInWork()
    {
        $request = Yii::$app->request;
        $success = true;
        $saved = array();
        if ($request->isPost) {
            $params = $request->bodyParams;
            $orders = Orders::findAll(['uuid' => $params]);
            foreach ($orders as $order) {
                $order->orderStatusUuid = OrderStatus::IN_WORK;
                if ($order->save()) {
                    $saved[] = [
                        '_id' => $order->_id,
                        'uuid' => $order->uuid,
                    ];
                } else {
                    $success = false;
                }
            }

            return ['success' => $success, 'data' => $saved];
        } else {
            throw new NotAcceptableHttpException();
        }
    }

    /**
     * Установка статуса наряда - Выполнен.
     *
     * @return array
     * @throws NotAcceptableHttpException
     */
    public function actionComplete()
    {
        $request = Yii::$app->request;
        $success = true;
        $saved = array();
        if ($request->isPost) {
            $params = $request->bodyParams;
            $orders = Orders::findAll(['uuid' => $params]);
            foreach ($orders as $order) {
                $order->orderStatusUuid = OrderStatus::COMPLETE;
                if ($order->save()) {
                    $saved[] = [
                        '_id' => $order->_id,
                        'uuid' => $order->uuid,
                    ];
                } else {
                    $success = false;
                }
            }

            return ['success' => $success, 'data' => $saved];
        } else {
            throw new NotAcceptableHttpException();
        }
    }

    /**
     * Установка статуса наряда - Не выполнен.
     *
     * @return array
     * @throws NotAcceptableHttpException
     */
    public function actionUnComplete()
    {
        $request = Yii::$app->request;
        $success = true;
        $saved = array();
        if ($request->isPost) {
            $params = $request->bodyParams;
            $orders = Orders::findAll(['uuid' => $params]);
            foreach ($orders as $order) {
                $order->orderStatusUuid = OrderStatus::UN_COMPLETE;
                if ($order->save()) {
                    $saved[] = [
                        '_id' => $order->_id,
                        'uuid' => $order->uuid,
                    ];
                } else {
                    $success = false;
                }
            }

            return ['success' => $success, 'data' => $saved];
        } else {
            throw new NotAcceptableHttpException();
        }
    }

    /**
     * Проверяем есть ли такой статус у нас.
     *
     * @param string $status uuid статуса.
     *
     * @return boolean Description
     */
    public static function checkStatus($status = null)
    {
        if ($status != null) {
            $statusList = array(
                OrderStatus::NEW_ORDER,
                OrderStatus::IN_WORK,
                OrderStatus::COMPLETE,
                OrderStatus::UN_COMPLETE,
                OrderStatus::CANCELED
            );
            return in_array($status, $statusList);
        } else {
            return false;
        }
    }
}
