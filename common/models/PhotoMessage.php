<?php
namespace common\models;

use common\components\IPhoto;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "photo_message".
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $messageUuid
 * @property double $latitude
 * @property double $longitude
 * @property string $createdAt
 * @property string $changedAt
 *
 * @property Message $message
 * @property string $photoUrl
 */
class PhotoMessage extends ActiveRecord implements IPhoto
{
    private static $_IMAGE_ROOT = 'message';

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
        return 'photo_message';
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
                    'messageUuid',
                ],
                'required'
            ],
/*            [['photo'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg'],*/
            [['latitude', 'longitude'], 'number'],
            [['uuid', 'messageUuid'], 'string', 'max' => 50],
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
            'messageUuid' => Yii::t('app', 'Сообщение'),
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
            'messageUuid',
            'message' => function ($model) {
                return $model->message;
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
    public function getMessage()
    {
        return $this->hasOne(Message::class, ['uuid' => 'messageUuid']);
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
