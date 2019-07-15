<?php

namespace api\controllers;

use api\components\BaseController;
use common\models\Documentation;
use yii\db\ActiveRecord;

class DocumentationController extends BaseController
{
    /** @var ActiveRecord $modelClass */
    public $modelClass = Documentation::class;

}
