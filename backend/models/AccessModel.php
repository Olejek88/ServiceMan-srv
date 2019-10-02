<?php

namespace backend\models;

use common\components\ZhkhActiveRecord;

class AccessModel extends ZhkhActiveRecord
{
    public $id;
    public $description;
    public $model;
    public $permission;
    public $admin = false;
    public $operator = false;
    public $dispatch = false;
    public $director = false;
}