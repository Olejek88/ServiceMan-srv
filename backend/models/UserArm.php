<?php

namespace backend\models;

use common\models\User;
use common\models\Users;
use Yii;
use yii\base\Model;

/**
 *
 */
class UserArm extends Model
{
    const SCENARIO_UPDATE = 'update';

    public $_id;
    public $oid;
    public $username;
    public $email;
    public $password;
    public $name;
    public $pass;
    public $type = 2;
    public $pin;
    public $active = 1;
    public $whoIs;
    public $contact;
    public $image;
    public $role = User::ROLE_OPERATOR;
    public $status = User::STATUS_ACTIVE;

    public function __construct($config = [])
    {
        parent::__construct($config);
        $this->oid = Users::getCurrentOid();
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username'], 'trim', 'on' => [self::SCENARIO_DEFAULT, self::SCENARIO_UPDATE]],
            [['username'], 'required', 'on' => [self::SCENARIO_DEFAULT, self::SCENARIO_UPDATE]],
            [['username'], 'unique', 'targetClass' => '\common\models\User', 'message' => 'Этот логин уже занят.',
                'on' => self::SCENARIO_DEFAULT],
            [['username'], 'unique', 'targetClass' => '\common\models\User', 'message' => 'Этот логин уже занят.',
                'on' => self::SCENARIO_UPDATE,
                'when' => function ($model) {
                    /** @var $model User */
                    $user = User::find()->where(['_id' => Yii::$app->request->get('id')])->one();
                    if ($user != null && $user->username == $model->username) {
                        return false;
                    } else {
                        return true;
                    }
                }],
            [['username'], 'string', 'min' => 2, 'max' => 255, 'on' => [self::SCENARIO_DEFAULT, self::SCENARIO_UPDATE]],

            [['password'], 'required', 'on' => self::SCENARIO_DEFAULT],
            [['password'], 'string', 'min' => 6, 'on' => self::SCENARIO_DEFAULT],
            [['password'], 'string', 'min' => 6, 'on' => self::SCENARIO_UPDATE, 'skipOnEmpty' => true],

            [['pin'], 'string', 'on' => [self::SCENARIO_DEFAULT, self::SCENARIO_UPDATE]],
            [['pin'], 'required', 'on' => self::SCENARIO_DEFAULT],
            [['pin'], 'required', 'on' => self::SCENARIO_UPDATE, 'skipOnEmpty' => true],

            [['name'], 'string', 'on' => [self::SCENARIO_DEFAULT, self::SCENARIO_UPDATE]],
            [['name'], 'required', 'on' => [self::SCENARIO_DEFAULT, self::SCENARIO_UPDATE]],

            [['type'], 'integer', 'on' => [self::SCENARIO_DEFAULT, self::SCENARIO_UPDATE]],
            [['type'], 'required', 'on' => [self::SCENARIO_DEFAULT, self::SCENARIO_UPDATE]],

            [['whoIs'], 'string', 'on' => [self::SCENARIO_DEFAULT, self::SCENARIO_UPDATE]],
            [['whoIs'], 'required', 'on' => [self::SCENARIO_DEFAULT, self::SCENARIO_UPDATE]],

            [['contact'], 'string', 'on' => [self::SCENARIO_DEFAULT, self::SCENARIO_UPDATE]],
            [['contact'], 'required', 'on' => [self::SCENARIO_DEFAULT, self::SCENARIO_UPDATE]],

            [['status'], 'default', 'value' => User::STATUS_ACTIVE, 'on' => [self::SCENARIO_DEFAULT, self::SCENARIO_UPDATE]],
            [['status'], 'in', 'range' => [User::STATUS_ACTIVE, User::STATUS_DELETED], 'on' => [self::SCENARIO_DEFAULT, self::SCENARIO_UPDATE]],

            [['role'], 'required', 'on' => [self::SCENARIO_DEFAULT, self::SCENARIO_UPDATE]],
            [['role'], 'string', 'max' => 128, 'on' => [self::SCENARIO_DEFAULT, self::SCENARIO_UPDATE]],
            [['role'], 'in', 'range' => [
                User::ROLE_ADMIN,
                User::ROLE_OPERATOR,
                User::ROLE_ANALYST,
                User::ROLE_USER,
            ], 'strict' => true, 'on' => [self::SCENARIO_DEFAULT, self::SCENARIO_UPDATE]],
        ];
    }

    /**
     * Метки для свойств.
     *
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'username' => Yii::t('app', 'Логин'),
            'password' => Yii::t('app', 'Пароль'),
            'name' => Yii::t('app', 'Имя'),
            'pass' => Yii::t('app', 'Пароль'),
            'type' => Yii::t('app', 'Тип'),
            'pin' => Yii::t('app', 'Пин'),
            'status' => Yii::t('app', 'Статус'),
            'whoIs' => Yii::t('app', 'Должность'),
            'image' => Yii::t('app', 'Фотография'),
            'contact' => Yii::t('app', 'Контакт'),
            'role' => Yii::t('app', 'Роль'),
        ];
    }

}
