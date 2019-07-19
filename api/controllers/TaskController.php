<?php

namespace api\controllers;

use api\components\BaseController;
use common\models\Task;
use common\models\User;
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


}
