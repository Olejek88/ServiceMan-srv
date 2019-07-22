<?php

namespace api\controllers;

use api\components\BaseController;
use common\models\DefectType;
use common\models\DocumentationType;
use yii\db\ActiveRecord;

class DocumentationTypeController extends BaseController
{
    /** @var ActiveRecord $modelClass */
    public $modelClass = DocumentationType::class;
}
