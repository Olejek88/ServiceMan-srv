<?php

namespace api\controllers;

use api\components\BaseController;
use common\models\Task;
use common\models\User;
use common\models\WorkStatus;
use Yii;
use yii\db\ActiveRecord;
use yii\web\NotAcceptableHttpException;

class TaskController extends BaseController
{
    /** @var ActiveRecord $modelClass */
    public $modelClass = Task::class;

    public function actionIndex()
    {
        $req = Yii::$app->request;

        /** @var ActiveRecord $class */
        $class = $this->modelClass;
        $query = $class::find();

        // задачи выбираем только для текущего пользователя
        /** @var User $identity */
        $identity = Yii::$app->user->identity;
        $query->leftJoin('{{%task_user}}', '{{%task_user}}.taskUuid = {{%task}}.uuid')
            ->andWhere(['{{%task_user}}.userUuid' => $identity->users->uuid]);

        // проверяем параметры запроса
        $uuid = $req->getQueryParam('uuid');
        if ($uuid != null) {
            $query->andWhere(['{{%task}}.uuid' => $uuid]);
        }

        $changedAfter = $req->getQueryParam('changedAfter');
        if ($changedAfter != null) {
            $query->andWhere(['>=', '{{%task}}.changedAt', $changedAfter]);
        }

        $status = $req->getQueryParam('status');
        if ($status != null) {
            $query->andWhere(['{{%task}}.workStatusUuid' => $status]);
        }

        // проверяем что хоть какие-то условия были заданы
        if ($query->where == null) {
            return [];
        }

//        $query->with(['equipment' => function ($query) {
//            /** @var ActiveQuery $query */
//            $query->with('object');
//        }]);
        $query->with(['author']);
        $query->with(['operations']);

        $result = $query->asArray()->all();
        return $result;

    }

    /**
     * Установка статуса задачи - В работе.
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
            $tasks = Task::findAll(['uuid' => $params]);
            foreach ($tasks as $task) {
                $task->workStatusUuid = WorkStatus::IN_WORK;
                if ($task->save()) {
                    $saved[] = [
                        '_id' => $task->_id,
                        'uuid' => $task->uuid,
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
     * Установка статуса задачи - Выполнена.
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
            $tasks = Task::findAll(['uuid' => $params]);
            foreach ($tasks as $task) {
                $task->workStatusUuid = WorkStatus::COMPLETE;
                $task->setWorkStatus();
                if ($task->save()) {
                    $saved[] = [
                        '_id' => $task->_id,
                        'uuid' => $task->uuid,
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
     * Установка статуса задачи - Не выполнена.
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
            $tasks = Task::findAll(['uuid' => $params]);
            foreach ($tasks as $task) {
                $task->workStatusUuid = WorkStatus::UN_COMPLETE;
                if ($task->save()) {
                    $saved[] = [
                        '_id' => $task->_id,
                        'uuid' => $task->uuid,
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

}
