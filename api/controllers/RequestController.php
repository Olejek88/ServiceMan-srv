<?php

namespace api\controllers;

use api\components\BaseController;
use common\models\Request;
use Yii;
use yii\db\ActiveRecord;

class RequestController extends BaseController
{
    /** @var ActiveRecord $modelClass */
    public $modelClass = Request::class;

    public function actionIndex()
    {
        $req = Yii::$app->request;

        /** @var ActiveRecord $class */
        $class = $this->modelClass;
        $query = $class::find();

        // проверяем параметры запроса
        $uuid = $req->getQueryParam('uuid');
        if ($uuid != null) {
            $query->andWhere(['{{%task}}.uuid' => $uuid]);
        }

        $changedAfter = $req->getQueryParam('changedAfter');
        if ($changedAfter != null) {
            $query->andWhere(['>=', 'changedAt', $changedAfter]);
        }

        // проверяем что хоть какие-то условия были заданы
        if ($query->where == null) {
            return [];
        }

        $query->with(['author']);
        $query->with(['user']);

        $result = $query->asArray()->all();
        return $result;

    }

}
