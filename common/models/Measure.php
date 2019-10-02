<?php

namespace common\models;

use common\components\ZhkhActiveRecord;
use Yii;
use yii\base\InvalidConfigException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\Exception;
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
 * @property ActiveQuery $measureType
 * @property Equipment $equipment
 */
class Measure extends ZhkhActiveRecord
{
    public const DESCRIPTION = 'Измерения';

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
            [['oid'], 'exist', 'targetClass' => Organization::class, 'targetAttribute' => ['oid' => 'uuid']],
            [['oid'], 'checkOrganizationOwn'],
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
        $fields = parent::fields();
        return $fields;
//        return ['_id', 'uuid',
//            'equipmentUuid',
//            'equipment' => function ($model) {
//                return $model->equipment;
//            },
//            'userUuid',
//            'user' => function ($model) {
//                return $model->user;
//            },
//            'measureTypeUuid',
//            'measureType' => function ($model) {
//                return $model->measureType;
//            },
//            'value',
//            'date',
//            'createdAt',
//            'changedAt',
//        ];
    }

    /**
     * Объект связанного поля.
     *
     * @return ActiveQuery
     */
    public function getEquipment()
    {
        return $this->hasOne(Equipment::class, ['uuid' => 'equipmentUuid']);
    }

    /**
     * Объект связанного поля.
     *
     * @return ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Users::class, ['uuid' => 'userUuid']);
    }

    /**
     * Объект связанного поля.
     *
     * @return ActiveQuery
     */
    public function getMeasureType()
    {
        return $this->hasOne(MeasureType::class, ['uuid' => 'measureTypeUuid']);
    }

    /**
     * @param $equipmentUuid
     * @param $startDate
     * @param $endDate
     * @return Measure|null
     * @throws InvalidConfigException
     * @throws Exception
     */
    public static function getLastMeasureBetweenDates($equipmentUuid, $startDate, $endDate)
    {
        $model = Measure::find()->where(["equipmentUuid" => $equipmentUuid])
            ->andWhere('date >= "' . $startDate . '"')
            ->andWhere('date < "' . $endDate . '"')
            ->orderBy('date DESC')
            ->one();
        return $model;
    }

    function getActionPermissions()
    {
        return array_merge(parent::getActionPermissions(), [
            'read' => [
                'trend',
                'add',
            ],
            'edit' => [
                'save',
            ]]);
    }
}
