<?php

namespace common\models;

use common\components\ZhkhActiveRecord;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * @property integer $_id @column pk|comment("Id")
 * @property string $uuid @column string(45)|unique|notNull
 * @property string $oid
 * @property string $alarmTypeUuid
 * @property string $alarmStatusUuid
 * @property string $objectUuid
 * @property string $userUuid
 * @property double $longitude
 * @property double $latitude
 * @property string $date @column string(200)|notNull|expr('CURRENT_TIMESTAMP')
 * @property string $createdAt @column string(200)|notNull|expr('CURRENT_TIMESTAMP')
 * @property string $changedAt @column string(200)|notNull|expr('CURRENT_TIMESTAMP')
 * @property string $comment
 *
 * @property Users $user
 * @property Object $object
 * @property AlarmStatus $alarmStatus
 * @property AlarmType $alarmType
 * @property Photo $photo
 */
class Alarm extends ZhkhActiveRecord
{
    public const DESCRIPTION = 'Предупреждения';

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
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'alarm';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uuid', 'alarmTypeUuid', 'alarmStatusUuid', 'userUuid', 'latitude', 'longitude', 'date'], 'required'],
            [['createdAt', 'changedAt', 'date'], 'safe'],
            [['uuid', 'alarmTypeUuid', 'oid', 'alarmStatusUuid', 'userUuid', 'objectUuid'], 'string', 'max' => 50],
            [['latitude', 'longitude'], 'double'],
            [['comment'], 'string', 'max' => 250],
            [['oid'], 'exist', 'targetClass' => Organization::class, 'targetAttribute' => ['oid' => 'uuid']],
            [['oid'], 'checkOrganizationOwn'],
        ];
    }

    public function fields()
    {
        $fields = parent::fields();
//        $fields['user'] = function ($model) {
//            return $model->user;
//        };
//        $fields['alarmStatus'] = function ($model) {
//            return $model->alarmStatus;
//        };
//        $fields['alarmType'] = function ($model) {
//            return $model->alarmType;
//        };
//        $fields['object'] = function ($model) {
//            return $model->object;
//        };
        return $fields;
    }

    public function getAlarmType()
    {
        return $this->hasOne(AlarmType::class, ['uuid' => 'alarmTypeUuid']);
    }

    public function getAlarmStatus()
    {
        return $this->hasOne(AlarmStatus::class, ['uuid' => 'alarmStatusUuid']);
    }

    public function getUser()
    {
        return $this->hasOne(Users::class, ['uuid' => 'userUuid']);
    }

    public function getObject()
    {
        return $this->hasOne(Objects::class, ['uuid' => 'objectUuid']);
    }

    public function getPhoto()
    {
        return $this->hasMany(Photo::class, ['objectUuid' => 'uuid']);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            '_id' => Yii::t('app', '№'),
            'uuid' => Yii::t('app', 'Uuid'),
            'date' => Yii::t('app', 'Дата возникновения'),
            'user' => Yii::t('app', 'Пользователь'),
            'userUuid' => Yii::t('app', 'Пользователь'),
            'alarmStatus' => Yii::t('app', 'Статус'),
            'alarmStatusUuid' => Yii::t('app', 'Статус'),
            'alarmType' => Yii::t('app', 'Тип события'),
            'alarmTypeUuid' => Yii::t('app', 'Тип события'),
            'longitude' => Yii::t('app', 'Долгота'),
            'latitude' => Yii::t('app', 'Широта'),
            'object' => Yii::t('app', 'Объект'),
            'objectUuid' => Yii::t('app', 'Объект'),
            'comment' => Yii::t('app', 'Описание'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
        ];
    }
}
