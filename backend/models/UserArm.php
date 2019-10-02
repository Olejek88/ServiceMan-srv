<?php

namespace backend\models;

use common\components\Tag;
use common\models\User;
use common\models\Users;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\db\Exception;
use yii\db\Query;

/**
 *
 */
class UserArm extends Model
{
    const SCENARIO_UPDATE = 'update';

    public $oid;
    public $username;
    public $email;
    public $password;
    public $name;
    public $type = Users::USERS_ARM;
    public $pin;
    public $active = User::STATUS_ACTIVE;
    public $whoIs;
    public $contact;
    public $image;
    public $role = User::ROLE_OPERATOR;
    public $status = User::STATUS_ACTIVE;
    public $tagType;

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

            [['password'], 'required', 'on' => self::SCENARIO_DEFAULT, 'when' => function ($model) {
                return $model->type == Users::USERS_ARM || $model->type == Users::USERS_ARM_WORKER;
            }, 'whenClient' => 'function(attribute, value){
              console.log("arm");
              return $("#userarm-type").val() == ' . Users::USERS_ARM . ';
            }'],
            [['password'], 'string', 'min' => 6, 'on' => self::SCENARIO_DEFAULT],
            [['password'], 'string', 'min' => 6, 'on' => self::SCENARIO_UPDATE, 'skipOnEmpty' => true],

            [['pin'], 'string', 'on' => [self::SCENARIO_DEFAULT, self::SCENARIO_UPDATE]],
            [['pin'], 'required', 'on' => [self::SCENARIO_DEFAULT, self::SCENARIO_UPDATE], 'when' => function ($model) {
                return $model->type == Users::USERS_WORKER || $model->type == Users::USERS_ARM_WORKER;
            }, 'whenClient' => 'function(attribute, value){
              console.log("worker");
              res = val == "' . Users::USERS_WORKER . '" || val == "' . Users::USERS_ARM_WORKER . '";
              console.log("res " + res);
              return res; 
            }'],
//            [['pin'], 'required', 'on' => self::SCENARIO_UPDATE, 'skipOnEmpty' => true],

            [['name'], 'string', 'on' => [self::SCENARIO_DEFAULT, self::SCENARIO_UPDATE]],
            [['name'], 'required', 'on' => [self::SCENARIO_DEFAULT, self::SCENARIO_UPDATE]],

            [['type'], 'integer', 'on' => [self::SCENARIO_DEFAULT, self::SCENARIO_UPDATE]],
            [['type'], 'in', 'range' => [Users::USERS_ARM, Users::USERS_WORKER, Users::USERS_ARM_WORKER],
                'on' => [self::SCENARIO_DEFAULT, self::SCENARIO_UPDATE]],
            [['type'], 'required', 'on' => [self::SCENARIO_DEFAULT, self::SCENARIO_UPDATE]],
            [['type'], 'checkLimit', 'on' => [self::SCENARIO_DEFAULT, self::SCENARIO_UPDATE]],

            [['whoIs'], 'string', 'on' => [self::SCENARIO_DEFAULT, self::SCENARIO_UPDATE]],
            [['whoIs'], 'required', 'on' => [self::SCENARIO_DEFAULT, self::SCENARIO_UPDATE]],

            [['contact'], 'string', 'on' => [self::SCENARIO_DEFAULT, self::SCENARIO_UPDATE]],
            [['contact'], 'required', 'on' => [self::SCENARIO_DEFAULT, self::SCENARIO_UPDATE]],

            [['status'], 'default', 'value' => User::STATUS_ACTIVE, 'on' => [self::SCENARIO_DEFAULT, self::SCENARIO_UPDATE]],
            [['status'], 'in', 'range' => [User::STATUS_ACTIVE, User::STATUS_DELETED], 'on' => [self::SCENARIO_DEFAULT, self::SCENARIO_UPDATE]],
            [['status'], 'checkLimit', 'on' => [self::SCENARIO_DEFAULT, self::SCENARIO_UPDATE]],

            [['role'], 'required', 'on' => [self::SCENARIO_DEFAULT, self::SCENARIO_UPDATE]],
            [['role'], 'string', 'max' => 128, 'on' => [self::SCENARIO_DEFAULT, self::SCENARIO_UPDATE]],
            [['role'], 'in', 'range' => [
                User::ROLE_ADMIN,
                User::ROLE_OPERATOR,
                User::ROLE_DISPATCH,
                User::ROLE_DIRECTOR,
            ], 'strict' => true, 'on' => [self::SCENARIO_DEFAULT, self::SCENARIO_UPDATE]],

            [['tagType'], 'string', 'on' => [self::SCENARIO_DEFAULT, self::SCENARIO_UPDATE]],
            [['tagType'], 'in', 'range' => [
                Tag::TAG_TYPE_PIN, Tag::TAG_TYPE_GRAPHIC_CODE, Tag::TAG_TYPE_NFC, Tag::TAG_TYPE_UHF
            ], 'on' => [self::SCENARIO_DEFAULT, self::SCENARIO_UPDATE]],
            [['tagType'], 'required', 'on' => [self::SCENARIO_DEFAULT, self::SCENARIO_UPDATE], 'when' => function ($model) {
                return $model->type == Users::USERS_WORKER || $model->type == Users::USERS_ARM_WORKER;
            }, 'whenClient' => 'function(attribute, value){
              console.log("tagType");
              val = $("#userarm-type").val();
              res = val == "' . Users::USERS_WORKER . '" || val == "' . Users::USERS_ARM_WORKER . '";
              console.log("res " + res);
              return res; 
            }'],
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
            'tagType' => Yii::t('app', 'Тип идентификатора'),
            'type' => Yii::t('app', 'Тип'),
            'pin' => Yii::t('app', 'Пин'),
            'status' => Yii::t('app', 'Статус'),
            'whoIs' => Yii::t('app', 'Должность'),
            'image' => Yii::t('app', 'Фотография'),
            'contact' => Yii::t('app', 'Контакт'),
            'role' => Yii::t('app', 'Роль'),
        ];
    }

    /**
     * @param $attr
     * @param $param
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function checkLimit($attr, $param)
    {
        if (in_array($this->type, [Users::USERS_WORKER, Users::USERS_ARM_WORKER])) {
            $limit = (new Query())
                ->select('*')
                ->from('{{%system_settings}}')
                ->where(['oid' => Users::getCurrentOid(), 'parameter' => 'workers_limit'])
                ->one();
            if ($limit == null) {
                $this->addError($attr, 'Создание мобильных пользователей ограничено.');
            }

            $users = Users::find()->where([
                'type' => [Users::USERS_WORKER, Users::USERS_ARM_WORKER],
                'user.status' => User::STATUS_ACTIVE,])
                ->leftJoin('user', 'users.user_id = user._id')
                ->all();
            if (count($users) >= $limit['value'] && $this->status == User::STATUS_ACTIVE) {
                $this->addError($attr, 'Создание мобильных пользователей ограничено значением ' . $limit['value']);
            }
        }
    }
}
