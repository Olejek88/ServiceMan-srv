<?php

namespace api\controllers;

use api\components\BaseController;
use common\models\Gpstrack;
use yii\db\ActiveRecord;

class GpsTrackController extends BaseController
{
    /** @var ActiveRecord $modelClass */
    public $modelClass = Gpstrack::class;
}
