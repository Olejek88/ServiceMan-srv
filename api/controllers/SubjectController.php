<?php

namespace api\controllers;

use api\components\BaseController;
use common\models\Subject;
use common\models\User;
use yii\db\ActiveRecord;

class SubjectController extends BaseController
{
    /** @var ActiveRecord $modelClass */
    public $modelClass = Subject::class;

    public function actionIndex()
    {
        $req = \Yii::$app->request;
        $user = \Yii::$app->user;
        $users = User::findIdentity($user->id)->users;

        /** @var ActiveRecord $class */
        $class = $this->modelClass;
        $query = $class::find();

        // проверяем параметры запроса
        $uuid = $req->getQueryParam('uuid');
        if ($uuid != null) {
            $query->andWhere(['`subject`.`uuid`' => $uuid]);
        }

        $changedAfter = $req->getQueryParam('changedAfter');
        if ($changedAfter != null) {
            $query->andWhere(['>=', '`subject`.`changedAt`', $changedAfter]);
        }

        // проверяем что хоть какие-то условия были заданы
        if ($query->where == null) {
            return [];
        }

        // выбираем данные из базы
        $query->select('subject.*')
            ->leftJoin('flat', '`subject`.`flatUuid` = `flat`.`uuid`')
            ->leftJoin('user_house', '`flat`.`houseUuid` = `user_house`.`houseUuid`')
            ->andWhere(['`user_house`.`userUuid`' => $users->uuid]);
        $result = $query->asArray()->all();

        return $result;
    }
}
