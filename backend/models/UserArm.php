<?php

namespace backend\models;

use Yii;
use yii\base\Model;

/**
 *
 */
class UserArm extends Model
{
    const UPDATE_SCENARIO = 'update';

    public $id;
    public $oid;
    public $username;
    public $email;
    public $password;
    public $uuid;
    public $name;
    public $login;
    public $pass;
    public $type;
    public $pin;
    public $active;
    public $whoIs;
    public $contact;
    public $user_id;
    public $image;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['username', 'trim'],
            ['username', 'required'],
            ['username', 'unique', 'targetClass' => '\common\models\User', 'message' => 'Этот логин уже занят.'],
            ['username', 'string', 'min' => 2, 'max' => 255],

            ['password', 'string', 'min' => 6],
            ['password', 'required', 'on' => 'default'],

            ['pin', 'string'],
            ['pin', 'required', 'on' => 'default'],

            ['name', 'string'],
            ['name', 'required'],

            ['type', 'integer'],
            ['type', 'required'],

            ['whoIs', 'string'],
            ['whoIs', 'required'],

            ['contact', 'string'],
            ['contact', 'required'],

            ['pass', 'string'],

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
            'id' => Yii::t('app', '№'),
            'username' => Yii::t('app', 'Логин'),
            'email' => Yii::t('app', 'email'),
            'password' => Yii::t('app', 'Пароль'),
            'uuid' => Yii::t('app', 'Uuid'),
            'name' => Yii::t('app', 'Имя'),
            'login' => Yii::t('app', 'Логин'),
            'pass' => Yii::t('app', 'Пароль'),
            'type' => Yii::t('app', 'Тип'),
            'pin' => Yii::t('app', 'Пин'),
            'active' => Yii::t('app', 'Статус'),
            'whoIs' => Yii::t('app', 'Должность'),
            'image' => Yii::t('app', 'Фотография'),
            'contact' => Yii::t('app', 'Контакт'),
            'userId' => Yii::t('app', 'User id'),
        ];
    }

}
