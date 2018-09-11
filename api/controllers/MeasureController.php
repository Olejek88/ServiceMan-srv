<?php

namespace api\controllers;

use api\components\BaseController;
use common\models\Equipment;
use common\models\Measure;
use yii\db\ActiveRecord;

class MeasureController extends BaseController
{
    /** @var ActiveRecord $modelClass */
    public $modelClass = Measure::class;

    public function actionCreate()
    {
        $success = true;
        $saved = array();
        $request = \Yii::$app->getRequest();
        $rawData = $request->getRawBody();
        $items = json_decode($rawData, true);
        foreach ($items as $item) {
            // сохраняем оборудование к которому привязано измерение
            $equipment = Equipment::findOne(['uuid' => $item['equipmentUuid']]);
            if ($equipment == null) {
                $equipment = new Equipment();
            }

            $equipment->setAttributes($item['equipment'], false);
            // просто тупо сохраняем, без проверки необходимости для этого
            // в оборудовании может быть изменены данные серийного номера, даты проверки и т.п.
            if ($equipment->save()) {
                $saved['equipment'] = [
                    '_id' => $equipment->_id,
                    'uuid' => $equipment->uuid,
                ];
            } else {
                $success = false;
            }

            // сохраняем измерение
            $line = Measure::findOne(['uuid' => $item['uuid']]);
            if ($line == null) {
                $line = new Measure();
            }

            $line->setAttributes($item, false);
            if ($line->save()) {
                $saved['measure'] = [
                    '_id' => $line->_id,
                    'uuid' => $line->uuid,
                ];
            } else {
                $success = false;
            }
        }

        return ['success' => $success, 'data' => $saved];
    }
}
