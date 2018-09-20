<?php

namespace api\controllers;

use api\components\BaseController;
use common\models\Journal;
use yii\db\ActiveRecord;

class JournalController extends BaseController
{
    /** @var ActiveRecord $modelClass */
    public $modelClass = Journal::class;

    public function actionIndex()
    {
        // данные журнала ни когда не отправляются на клиента
        return [];
    }

    public function actionCreate()
    {
        $request = \Yii::$app->request;

        // запись для загружаемого файла
        $rawData = $request->getRawBody();
        $items = json_decode($rawData, true);
        $result = self::createSimpleObjects($items);

        return $result;
    }

    protected function createSimpleObjects($items)
    {
        $success = true;
        $saved = array();
        foreach ($items as $item) {
            $old_id = $item['_id'];
            $line = self::createSimpleObject($item);
            if ($line->save()) {
                $saved[] = [
                    '_id' => $old_id,
                ];
            } else {
                $success = false;
            }
        }

        return ['success' => $success, 'data' => $saved];
    }

    protected function createSimpleObject($item)
    {
        /** @var ActiveRecord $class */
        /** @var ActiveRecord $line */
        $class = $this->modelClass;
        unset($item['_id']);
        $line = new $class;
        $line->setAttributes($item, false);
        return $line;
    }
}
