<?php

namespace api\controllers;

use Yii;
use api\components\BaseController;
use common\models\Organization;
use common\models\User;
use common\models\Users;
use yii\db\ActiveRecord;

class UsersController extends BaseController
{
    /** @var ActiveRecord $modelClass */
    public $modelClass = Users::class;

    public function actionIndex()
    {
        $req = Yii::$app->request;

        /** @var ActiveRecord $class */
        $class = $this->modelClass;
        $query = $class::find();

        // проверяем параметры запроса
        $uuid = $req->getQueryParam('uuid');
        if ($uuid != null) {
            $query->andWhere(['uuid' => $uuid]);
        }

        $changedAfter = $req->getQueryParam('changedAfter');
        if ($changedAfter != null) {
            $query->andWhere(['>=', 'changedAt', $changedAfter]);
        }

        // проверяем что хоть какие-то условия были заданы
        if ($query->where == null) {
            return [];
        }

        /** @var User $user */
        $user = Yii::$app->user->identity;

        if ($user->users->uuid == Users::USER_SERVICE_UUID) {
            $headers = $req->getHeaders();
            $orgId = $headers->get('x-org');
            $data = $headers->get('x-data');
            $testHash = $headers->get('x-hash');
            $org = Organization::findOne(['_id' => $orgId]);
            if ($org != null) {
                $query->andWhere(['oid' => $org->uuid]);
                $data4hash = $data . ':' . $org->_id . ':' . $org->secret;
                if ($testHash != md5($data4hash)) {
                    return [];
                }
            } else {
                return [];
            }
        }

        $query->andWhere(['type' => Users::USERS_WORKER]);

        // выбираем данные из базы
        $result = $query->with('organization')->asArray()->all();
        return $result;
    }
}
