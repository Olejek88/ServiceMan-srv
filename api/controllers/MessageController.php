<?php

namespace api\controllers;

use api\components\BaseController;
use common\models\Message;
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
}
