<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "alarm".
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $oid
 * @property string $alarmTypeUuid
 * @property string $alarmStatusUuid
 * @property string $objectUuid
 * @property string $userUuid
 * @property double $longitude
 * @property double $latitude
 * @property string $date
 * @property string $createdAt
 * @property string $changedAt
 * @property string $comment
 *
 * @property Users $user
 * @property Object $object
 * @property AlarmStatus $alarmStatus
 * @property AlarmType $alarmType
 * @property Photo $photo
 */
class Alarm extends ActiveRecord
{
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
            [['uuid', 'alarmTypeUuid', 'alarmStatusUuid', 'userUuid' , 'objectUuid'], 'string', 'max' => 50],
            [['latitude', 'longitude'], 'double'],
            [['comment'], 'string', 'max' => 250],
        ];
    }

    public function fields()
    {
        return [
            '_id',
            'uuid',
            'userUuid',
            'user' => function ($model) {
                return $model->user;
            },
            'date',
            'alarmStatusUuid',
            'alarmStatus' => function ($model) {
                return $model->alarmStatus;
            },
            'alarmTypeUuid',
            'alarmType' => function ($model) {
                return $model->alarmType;
            },
            'objectUuid',
            'object' => function ($model) {
                return $model->object;
            },
            'longitude',
            'latitude',
            'comment',
            'createdAt',
            'changedAt',
        ];
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

    public function getPhoto() {
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
            'comment' => Yii::t('app', 'Описание'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
        ];
    }
}
