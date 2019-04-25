<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "measure".
 *
 * @property integer $_id
 * @property string $oid идентификатор организации
 * @property string $uuid
 * @property string $equipmentUuid
 * @property string $userUuid
 * @property string $measureTypeUuid
 * @property double $value
 * @property string $date
 * @property string $createdAt
 * @property string $changedAt
 *
 * @property Users $user
 * @property Equipment $equipment
 */
class Measure extends ActiveRecord
{

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
        return 'measure';
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
                    'equipmentUuid',
                    'userUuid',
                    'measureTypeUuid',
                    'value',
                    'date'
                ],
                'required'
            ],
            [['value'], 'number'],
            [['uuid', 'equipmentUuid', 'userUuid', 'date', 'oid'], 'string', 'max' => 50],
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
            'equipmentUuid' => Yii::t('app', 'Оборудование'),
            'measureTypeUuid' => Yii::t('app', 'Тип измерения'),
            'userUuid' => Yii::t('app', 'Пользователь'),
            'equipment' => Yii::t('app', 'Оборудование'),
            'user' => Yii::t('app', 'Пользователь'),
            'value' => Yii::t('app', 'Значение'),
            'date' => Yii::t('app', 'Дата'),
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
        return ['_id', 'uuid',
            'equipmentUuid',
            'equipment' => function ($model) {
                return $model->equipment;
            },
            'userUuid',
            'user' => function ($model) {
                return $model->user;
            },
            'measureTypeUuid',
            'measureType' => function ($model) {
                return $model->measureType;
            },
            'value',
            'date',
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
    public function getEquipment()
    {
        return $this->hasOne(Equipment::class, ['uuid' => 'equipmentUuid']);
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
     * Объект связанного поля.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMeasureType()
    {
        return $this->hasOne(MeasureType::class, ['uuid' => 'measureTypeUuid']);
    }

    public static function getLastMeasureBetweenDates($equipmentUuid, $startDate, $endDate)
    {
        $model = Measure::find()->where(["equipmentUuid" => $equipmentUuid])
            ->andWhere('date >= "'.$startDate.'"')
            ->andWhere('date < "'.$endDate.'"')
            ->orderBy('date DESC')
            ->one();
        return $model;
    }

}
