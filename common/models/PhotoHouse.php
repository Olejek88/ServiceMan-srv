<?php
namespace common\models;

use common\components\IPhoto;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "photo_house".
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $houseUuid
 * @property string $userUuid
 * @property double $latitude
 * @property double $longitude
 * @property string $createdAt
 * @property string $changedAt
 *
 * @property Users $user
 * @property House $house
 * @property string $photoUrl
 */
class PhotoHouse extends ActiveRecord implements IPhoto
{
    private static $_IMAGE_ROOT = 'house';

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
        return 'photo_house';
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
                    'houseUuid',
                    'userUuid',
                ],
                'required'
            ],
/*            [['photo'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg'],*/
            [['latitude', 'longitude'], 'number'],
            [['uuid', 'houseUuid', 'userUuid'], 'string', 'max' => 50],
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
            'houseUuid' => Yii::t('app', 'Дом'),
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
        return ['_id','uuid',
            'houseUuid',
            'house' => function ($model) {
                return $model->house;
            },
            'userUuid',
            'user' => function ($model) {
                return $model->user;
            },
            'latitude',
            'longitude',
            'createdAt',
            'changedAt',
        ];
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
     * Объект связанного поля.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getHouse()
    {
        return $this->hasOne(House::class, ['uuid' => 'houseUuid']);
    }

    /**
     * Объект связанного поля.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Users::class, ['uuid' => 'userUuid']);
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
     * Каталог где хранится изображение.
     *
     * @return string
     */
    public static function getImageRoot()
    {
        return self::$_IMAGE_ROOT;
    }
}
