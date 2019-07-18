<?php

namespace api\controllers;

use api\components\BaseController;
use common\models\Documentation;
use common\models\Equipment;
use Yii;
use yii\db\ActiveRecord;

class DocumentationController extends BaseController
{
    /** @var ActiveRecord $modelClass */
    public $modelClass = Documentation::class;

    public function actionIndex()
    {
        $req = Yii::$app->request;
        $eqTbl = Equipment::tableName();
        $docTbl = Documentation::tableName();

        /** @var ActiveRecord $class */
        $class = $this->modelClass;
        $query = $class::find();

        // проверяем параметры запроса
        $uuid = $req->getQueryParam($docTbl . '.uuid');
        if ($uuid != null) {
            $query->andWhere(['uuid' => $uuid]);
        }

        $eqUuids = $req->getQueryParam('eqUuids');
        if ($eqUuids != null) {
            $query->leftJoin($eqTbl,
                $docTbl . '.equipmentUuid = ' . $eqTbl . '.uuid' .
                ' or ' .
                $docTbl . '.equipmentTypeUuid = ' . $eqTbl . '.equipmentTypeUuid'
            );
            $query->andWhere([$eqTbl . '.uuid' => $eqUuids]);

        }

        $changedAfter = $req->getQueryParam('changedAfter');
        if ($changedAfter != null) {
            $query->andWhere(['>=', $docTbl . '.changedAt', $changedAfter]);
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
