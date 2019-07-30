<?php

namespace common\models;

use common\components\IPhoto;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "photo".
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $objectUuid
 * @property string $userUuid
 * @property double $latitude
 * @property double $longitude
 * @property string $createdAt
 * @property string $changedAt
 *
 * @property Users $user
 * @property Objects $object
 * @property string $imagePath
 * @property string $imageUrl
 */
class Photo extends ActiveRecord implements IPhoto
{
    private static $_IMAGE_ROOT = 'photo';

    /**
     * Behaviors
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
     * Название таблицы
     *
     * @inheritdoc
     *
     * @return string
     */
    public static function tableName()
    {
        return 'photo';
    }

    /**
     * Rules
     *
     * @inheritdoc
     *
     * @return array
     */
    public function rules()
    {
        return [
            [
                [
                    'uuid',
                    'userUuid',
                ],
                'required'
            ],
            /*            [['photo'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg'],*/
            [['latitude', 'longitude'], 'number'],
            [['uuid', 'objectUuid', 'userUuid'], 'string', 'max' => 50],
            [['createdAt', 'changedAt'], 'safe'],
        ];
    }

    /**
     * Названия отрибутов
     *
     * @inheritdoc
     *
     * @return array
     */
    public function attributeLabels()
    {
        return [
            '_id' => Yii::t('app', '№'),
            'uuid' => Yii::t('app', 'Uuid'),
            'objectUuid' => Yii::t('app', 'Объект'),
            'userUuid' => Yii::t('app', 'Пользователь'),
            'latitude' => Yii::t('app', 'Широта'),
            'longitude' => Yii::t('app', 'Долгота'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
        ];
    }

    /**
     * Fields
     *
     * @return array
     */
    public function fields()
    {
        $fields = parent::fields();
        return $fields;
//        return ['_id','uuid',
//            'objectUuid',
//            'object' => function ($model) {
//                return $model->object;
//            },
//            'userUuid',
//            'user' => function ($model) {
//                return $model->user;
//            },
//            'latitude',
//            'longitude',
//            'createdAt',
//            'changedAt',
//        ];
    }

    /**
     * Объект связанного поля.
     *
     * @return ActiveQuery
     */
    public function getObject()
    {
        return $this->hasOne(Objects::class, ['uuid' => 'objectUuid']);
    }

    /**
     * Объект связанного поля.
     *
     * @return ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['uuid' => 'userUuid']);
    }

    /**
     * URL изображения.
     *
     * @return string
     */
    public function getImageUrl()
    {
        $localPath = self::$_IMAGE_ROOT . '/' . $this->objectUuid . '/' . $this->uuid . '.jpg';
        if (file_exists(Yii::getAlias('@storage/' . $localPath))) {
            $url = $localPath;
        } else {
            $url = null;
        }

        return $url;
    }

    public function getImagePath()
    {
        $localPath = self::$_IMAGE_ROOT . '/' . $this->objectUuid;
        $path = Yii::getAlias('@storage/' . $localPath);
        return $path;
    }

    /**
     * Каталог где хранится изображение.
     *
     * @return string
     */
    public static function getImageRoot()
    {
        return self::$_IMAGE_ROOT;
    }
}
