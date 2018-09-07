<?php

namespace api\controllers;

use api\components\BaseController;
use common\models\Resident;
use yii\db\ActiveRecord;

class ResidentController extends BaseController
{
    /** @var ActiveRecord $modelClass */
    public $modelClass = Resident::class;
}
