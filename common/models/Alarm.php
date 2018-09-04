<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\db\ActiveRecord;
/**
 * This is the model class for table "alarm".
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $alarmTypeUuid
 * @property string $alarmStatusUuid
 * @property string $userUuid
 * @property double $longitude
 * @property double $latitude
 * @property string $date
 * @property string $createdAt
 * @property string $changedAt
 */
class Alarm extends ActiveRecord
{
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
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
            [['uuid', 'alarmTypeUuid', 'alarmStatusUuid', 'userUuid'], 'string', 'max' => 50],
            [['latitude', 'longitude'], 'double'],
        ];
    }

    public function fields()
    {
        return [
            '_id',
            'uuid',
            'user' => function ($model) {
                return $model->user;
            },
            'date',
            'alarmStatus' => function ($model) {
                return $model->alarmStatus;
            },
            'alarmType' => function ($model) {
                return $model->alarmType;
            },
            'longitude',
            'latitude',
            'createdAt',
            'changedAt',
        ];
    }

    public function getAlarmType()
    {
        return $this->hasOne(AlarmType::className(), ['uuid' => 'alarmTypeUuid']);
    }
    public function getAlarmStatus()
    {
        return $this->hasOne(AlarmStatus::className(), ['uuid' => 'alarmStatusUuid']);
    }
    public function getUser()
    {
        return $this->hasOne(Users::className(), ['uuid' => 'userUuid']);
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
            'alarmType' => Yii::t('app', 'Тип события'),
            'alarmStatusUuid' => Yii::t('app', 'Статус'),
            'alarmTypeUuid' => Yii::t('app', 'Тип события'),
            'longitude' => Yii::t('app', 'Долгота'),
            'latitude' => Yii::t('app', 'Широта'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
        ];
    }
}
