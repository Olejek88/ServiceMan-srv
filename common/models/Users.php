<?php

namespace common\models;

use common\components\ZhkhActiveRecord;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\Expression;

/**
 * Class Users
 *
 * @property integer $_id
 * @property string $oid идентификатор организации
 * @property string $uuid
 * @property string $type
 * @property string $name
 * @property string $whoIs
 * @property string $pin
 * @property integer $active
 * @property string $contact
 * @property integer $user_id
 * @property integer $createdAt
 * @property integer $changedAt
 * @property string $image
 *
 * @property integer $id
 * @property string $photoUrl
 * @property null|string $imageDir
 * @property User $user
 * @property Organization|null $organization
 */
class Users extends ZhkhActiveRecord
{
    private static $_IMAGE_ROOT = 'users';
    public const USER_SERVICE_UUID = '00000000-9BF0-4542-B127-F4ECEFCE49DA';

    public const USERS_ARM = 1;
    public const USERS_WORKER = 2;

    /**
     * Behaviors.
     *
     * @return array
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'createdAt',
                'updatedAtAttribute' => 'changedAt',
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * Table name.
     *
     * @return string
     */
    public static function tableName()
    {
        return 'users';
    }

    /**
     * Rules.
     *
     * @return array
     */
    public function rules()
    {
        return [
            [
                [
                    'uuid',
                    'name',
                    'type',
                    'active',
                    'pin',
                    'contact'
                ],
                'required', 'on' => ['default', 'signup'],
            ],
            [['image'], 'file', 'on' => ['default', 'signup'],],
            [['user_id', 'type', 'active'], 'integer', 'on' => ['default', 'signup'],],
            [['createdAt', 'changedAt'], 'safe', 'on' => ['default', 'signup'],],
            [['uuid', 'pin', 'whoIs', 'oid'], 'string', 'max' => 45, 'on' => ['default', 'signup'],],
            [['name', 'contact'], 'string', 'max' => 100, 'on' => ['default', 'signup'],],
            [['oid'], 'exist', 'targetClass' => Organization::class, 'targetAttribute' => ['oid' => 'uuid'], 'on' => ['default', 'signup'],],
            [['oid'], 'checkOrganizationOwn', 'on' => 'default'],
            [['user_id'], 'exist', 'targetClass' => User::class, 'targetAttribute' => ['user_id' => '_id'], 'on' => ['default', 'signup'],],
            [['user_id'], 'unique', 'on' => ['default', 'signup'],],
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
            '_id' => Yii::t('app', '№'),
            'uuid' => Yii::t('app', 'Uuid'),
            'name' => Yii::t('app', 'Имя'),
            'type' => Yii::t('app', 'Тип пользователя'),
            'active' => Yii::t('app', 'Активен'),
            'pin' => Yii::t('app', 'Хеш пин кода'),
            'image' => Yii::t('app', 'Фото'),
            'contact' => Yii::t('app', 'Контакт'),
            'user_id' => Yii::t('app', 'User id'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
        ];
    }

    /**
     * Свойства объекта со связанными данными.
     *
     * @return array
     */
    public function fields()
    {
        $fields = parent::fields();
        return $fields;
//        return [
//            '_id',
//            'uuid',
//            'organization' => function ($model) {
//                return $model->organization;
//            },
//            'name',
//            'active',
//            'type',
//            'pin',
//            'user_id',
//            'contact',
//            'active',
//            'user' => function ($model) {
//                return $model->user;
//            },
//            'createdAt',
//            'changedAt',
//            'image',
//        ];
    }

    /**
     * Связываем пользователей из yii с пользователями из sman.
     *
     * @return ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['_id' => 'user_id']);
    }

    /**
     * Возвращает id.
     *
     * @return mixed
     */
    public function getId()
    {
        return $this['_id'];
    }

    /**
     * URL изображения.
     *
     * @return string | null
     */
    public function getImageDir()
    {
        $localPath = 'storage/' . self::$_IMAGE_ROOT . '/';
        return $localPath;
    }

    /**
     * URL изображения.
     *
     * @return string
     */
    public function getPhotoUrl()
    {
        $localPath = '/storage/' . self::$_IMAGE_ROOT . '/' . $this->uuid . '.jpg';
        if (file_exists(Yii::getAlias('@backend/web/' . $localPath))) {
            $url = $localPath;
        } else {
            $url = null;
        }

        return $url;
    }

    /**
     * @return ActiveQuery
     */
    public function getOrganization()
    {
        return $this->hasOne(Organization::class, ['uuid' => 'oid']);
    }

    /**
     * @return string
     */
    static function getCurrentOid()
    {
        /** @var User $identity */
        $identity = Yii::$app->user->identity;
        $oid = $identity->users->oid;
        return $oid;
    }
}
