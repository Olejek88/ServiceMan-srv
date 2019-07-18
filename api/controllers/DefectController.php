<?php

namespace api\controllers;

use api\components\BaseController;
use common\models\Defect;
use Yii;
use yii\db\ActiveRecord;

class DefectController extends BaseController
{
    /** @var ActiveRecord $modelClass */
    public $modelClass = Defect::class;

    public function actionIndex()
    {
        $req = Yii::$app->request;

        /** @var ActiveRecord $class */
        $class = $this->modelClass;
        $query = $class::find();

        // проверяем параметры запроса
        $uuid = $req->getQueryParam('uuid');
        if ($uuid != null) {
            if (is_array($uuid)) {
                $query->andWhere(['equipment.uuid' => $uuid]);
            } else {
                $query->andWhere(['uuid' => $uuid]);
            }
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
        $result = $query->asArray()->all();

        return $result;
    }
}
