<?php

namespace api\controllers;

use api\components\BaseController;
use common\models\Message;
use common\models\User;
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

        /** @var User $identity */
        $identity = Yii::$app->user->identity;

        // выбираем сообщения только для текущего пользователя
        $query->andWhere(['toUserUuid' => $identity->users->uuid]);

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

        $query->with(['fromUser']);
        $query->with(['toUser']);

        $result = $query->asArray()->all();
        return $result;
    }
}
