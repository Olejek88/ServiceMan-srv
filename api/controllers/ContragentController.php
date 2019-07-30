<?php

namespace api\controllers;

use api\components\BaseController;
use common\models\Contragent;
use yii\db\ActiveRecord;

class ContragentController extends BaseController
{
    /** @var ActiveRecord $modelClass */
    public $modelClass = Contragent::class;
}
