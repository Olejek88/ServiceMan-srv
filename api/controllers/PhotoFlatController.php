<?php

namespace api\controllers;

use api\components\BaseController;
use common\models\PhotoFlat;
use yii\db\ActiveRecord;

class PhotoFlatController extends BaseController
{
    /** @var ActiveRecord $modelClass */
    public $modelClass = PhotoFlat::class;
}
