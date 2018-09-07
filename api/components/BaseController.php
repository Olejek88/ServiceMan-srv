<?php

namespace api\components;

use yii\db\ActiveRecord;
use yii\filters\auth\HttpBearerAuth;
use yii\rest\Controller;

class BaseController extends Controller
{
    public $modelClass = ActiveRecord::class;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator']['class'] = HttpBearerAuth::class;
        return $behaviors;
    }

    public function actionIndex()
    {
        // проверяем параметры запроса
        $req = \Yii::$app->request;

        /** @var ActiveRecord $class */
        $class = $this->modelClass;
        $query = $class::find();

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

        // проверяем что хоть какие-то условия были заданы
        if ($query->where == null) {
            return [];
        }

        // выбираем данные из базы
        $result = $query->all();
        return $result;
    }
}
