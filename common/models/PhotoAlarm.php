<?php
namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "photo_alarm".
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $alarmUuid
 * @property string $userUuid
 * @property double $latitude
 * @property double $longitude
 * @property string $createdAt
 * @property string $changedAt
 *
 * @property Users $user
 * @property Alarm $alarm
 * @property string $photoUrl
 */
class PhotoAlarm extends ActiveRecord
{
    private static $_IMAGE_ROOT = 'alarm';

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
        return 'photo_alarm';
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
                    'alarmUuid',
                    'userUuid',
                ],
                'required'
            ],
/*            [['photo'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg'],*/
            [['latitude', 'longitude'], 'number'],
            [['uuid', 'alarmUuid', 'userUuid'], 'string', 'max' => 50],
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
            'alarmUuid' => Yii::t('app', 'Событие'),
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
            'alarm' => function ($model) {
                return $model->alarm;
            },
            'alarmUuid',
            'user' => function ($model) {
                return $model->user;
            },
            'userUuid',
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
    public function getAlarm()
    {
        return $this->hasOne(Alarm::class, ['uuid' => 'alarmUuid']);
    }

    /**
     * Объект связанного поля.
     *
     * @return \yii\db\ActiveQuery
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
    public static function getPhotoDir()
    {
        return self::$_IMAGE_ROOT;
    }

    /**
     * Сохраняет загруженый через форму файл.
     *
     * @param string $fileName
     * @param string $fileElementName
     * @return boolean
     */
    public static function saveUploadFile($fileName, $fileElementName = 'file')
    {
        $dir = \Yii::getAlias('@storage/') . self::$_IMAGE_ROOT;
        if (!is_dir($dir)) {
            if (!mkdir($dir, 0755, true)) {
                return false;
            }
        }

        return move_uploaded_file($_FILES[$fileElementName]['tmp_name'], $dir . '/' . $fileName);
    }
}