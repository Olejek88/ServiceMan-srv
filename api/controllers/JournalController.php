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


}
