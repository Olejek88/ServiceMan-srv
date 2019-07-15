<?php

namespace api\controllers;

use api\components\BaseController;
use common\models\Message;
use common\models\Users;
use Yii;
use yii\db\ActiveRecord;

class MessageController extends BaseController
{
    /** @var ActiveRecord $modelClass */
    public $modelClass = Message::class;

    /**
     * @return array
     */
    public function actionCreate()
    {
        return parent::createBase();
    }

    public function actionIndex()
    {
        $req = Yii::$app->request;

        /** @var ActiveRecord $class */
        $class = $this->modelClass;
        $query = $class::find();

        // выбираем сообщения только для текущего пользователя
        $query->andWhere(['toUserUuid' => Users::getCurrentOid()]);

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

        $result = $query->all();
        return $result;
    }
}
