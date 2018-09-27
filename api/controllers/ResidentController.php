<?php

namespace api\controllers;

use api\components\BaseController;
use common\models\Resident;
use common\models\User;
use yii\db\ActiveRecord;

class ResidentController extends BaseController
{
    /** @var ActiveRecord $modelClass */
    public $modelClass = Resident::class;

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
            $query->andWhere(['`resident`.`uuid`' => $uuid]);
        }

        $changedAfter = $req->getQueryParam('changedAfter');
        if ($changedAfter != null) {
            $query->andWhere(['>=', '`resident`.`changedAt`', $changedAfter]);
        }

        // проверяем что хоть какие-то условия были заданы
        if ($query->where == null) {
            return [];
        }

        // выбираем данные из базы
        $query->select('resident.*')
            ->leftJoin('flat', '`resident`.`flatUuid` = `flat`.`uuid`')
            ->leftJoin('user_house', '`flat`.`houseUuid` = `user_house`.`houseUuid`')
            ->andWhere(['`user_house`.`userUuid`' => $users->uuid]);
        $result = $query->asArray()->all();

        return $result;
    }
}
