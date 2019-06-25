<?php

namespace backend\models;

use yii\base\Model;
use common\models\User;

class Role extends Model
{
    public $role;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['role'], 'string', 'max' => 128],
            ['role', 'in', 'range' => [
                User::ROLE_ADMIN,
                User::ROLE_OPERATOR,
                User::ROLE_ANALYST,
                User::ROLE_USER,
            ],
                'strict' => true],
        ];
    }
}