<?php

namespace api\controllers;

use api\components\BaseController;
use common\models\Task;
use common\models\Users;
use Yii;
use yii\db\ActiveRecord;

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
        $query->leftJoin('{{%task_user}}', '{{%task_user}}.taskUuid = {{%task}}.uuid')
            ->andWhere(['{{%task_user}}.userUuid' => Users::getCurrentOid()]);

        // проверяем параметры запроса
        $uuid = $req->getQueryParam('uuid');
        if ($uuid != null) {
            $query->andWhere(['{{%task}}.uuid' => $uuid]);
        }

        $changedAfter = $req->getQueryParam('changedAfter');
        if ($changedAfter != null) {
            $query->andWhere(['>=', '{{%task}}.changedAt', $changedAfter]);
        }

        // проверяем что хоть какие-то условия были заданы
        if ($query->where == null) {
            return [];
        }


        $result = $query->all();
        return $result;

    }


}
