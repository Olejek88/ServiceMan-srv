<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * Class Users
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $type
 * @property string $name
 * @property string $pin
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
 */
class Users extends ActiveRecord
{
    private static $_IMAGE_ROOT = 'users';
    public const USER_SERVICE_UUID = '00000000-9BF0-4542-B127-F4ECEFCE49DA';

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
                    'pin',
                    'contact'
                ],
                'required'
            ],
            [['image'], 'file'],
            [['user_id','type'], 'integer'],
            [['createdAt', 'changedAt'], 'safe'],
            [['uuid', 'pin'], 'string', 'max' => 50],
            [['name', 'contact'], 'string', 'max' => 100],
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
        return [
            '_id',
            'uuid',
            'name',
            'type',
            'pin',
            'user_id',
            'contact',
            'user' => function ($model) {
                return $model->user;
            },
            'createdAt',
            'changedAt',
            'image',
        ];
    }

    /**
     * Связываем пользователей из yii с пользователями из sman.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['_id' => 'user_id']);
    }

    /**
     * Проверка целостности модели?
     *
     * @return bool
     */
    public function upload()
    {
        if ($this->validate()) {
            return true;
        } else {
            return false;
        }
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
}
