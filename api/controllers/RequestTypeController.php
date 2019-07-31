<?php

namespace api\controllers;

use api\components\BaseController;
use common\models\RequestType;
use common\models\User;
use Yii;
use yii\db\ActiveRecord;

class RequestTypeController extends BaseController
{
    /** @var ActiveRecord $modelClass */
    public $modelClass = RequestType::class;

    public function actionIndex()
    {
        $req = Yii::$app->request;

        /** @var ActiveRecord $class */
        $class = $this->modelClass;
        $query = $class::find();

        // задачи выбираем только для текущего пользователя
        /** @var User $identity */
        $identity = Yii::$app->user->identity;
        $query->leftJoin('{{%task_template}}', '{{%task_template}}.uuid = {{%request_type}}.taskTemplateUuid')
            ->andWhere(['{{%task_template}}.oid' => $identity->users->oid]);

        $changedAfter = $req->getQueryParam('changedAfter');
        if ($changedAfter != null) {
            $query->andWhere(['>=', '{{%request_type}}.changedAt', $changedAfter]);
        }

        // проверяем что хоть какие-то условия были заданы
        if ($query->where == null) {
            return [];
        }

        $result = $query->asArray()->all();
        return $result;

    }

}
