<?php

namespace api\controllers;

use api\components\BaseController;
use common\models\User;
use common\models\UserHouse;
use yii\db\ActiveRecord;

class UserHouseController extends BaseController
{
    /** @var ActiveRecord $modelClass */
    public $modelClass = UserHouse::class;

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
            $query->andWhere(['`user_house`.`uuid`' => $uuid]);
        }

        $changedAfter = $req->getQueryParam('changedAfter');
        if ($changedAfter != null) {
            $query->andWhere(['>=', '`user_house`.`changedAt`', $changedAfter]);
        }

        // проверяем что хоть какие-то условия были заданы
        if ($query->where == null) {
            return [];
        }

        // выбираем данные из базы
        $query->andWhere(['`user_house`.`userUuid`' => $users->uuid]);
        $result = $query->asArray()->all();

        return $result;
    }
}
