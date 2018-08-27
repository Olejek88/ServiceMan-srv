<?php
/**
 * PHP Version 7.0
 *
 * @category Category
 * @package  Common\models
 * @author   Максим Шумаков <ms.profile.d@gmail.com>
 * @license  http://www.yiiframework.com/license/ License name
 * @link     http://www.toirus.ru
 */

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "measuredvalue".
 *
 * @category Category
 * @package  Common\models
 * @author   Максим Шумаков <ms.profile.d@gmail.com>
 * @license  http://www.yiiframework.com/license/ License name
 * @link     http://www.toirus.ru
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $equipmentUuid
 * @property string $operationUuid
 * @property string $date
 * @property string $value
 * @property string $createdAt
 * @property string $changedAt
 * @property string $measureTypeUuid
 */
class MeasuredValue extends ActiveRecord
{
    /**
     * Название таблицы.
     *
     * @return string
     *
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'measured_value';
    }

    /**
     * Rules.
     *
     * @return array
     *
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    'uuid',
                    'equipmentUuid',
                    'operationUuid',
                    'measureTypeUuid',
                    'value'
                ],
                'required'
            ],
            [['date', 'createdAt', 'changedAt'], 'safe'],
            [
                [
                    'uuid',
                    'equipmentUuid',
                    'operationUuid',
                    'measureTypeUuid',
                    'value'
                ],
                'string',
                'max' => 45
            ],
        ];
    }

    /**
     * Labels.
     *
     * @return array
     *
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            '_id' => Yii::t('app', '№'),
            'uuid' => Yii::t('app', 'Uuid'),
            'equipmentUuid' => Yii::t('app', 'Оборудование'),
            'operationUuid' => Yii::t('app', 'Операция'),
            'measureTypeUuid' => Yii::t('app', 'Тип измерения'),
            'date' => Yii::t('app', 'Дата'),
            'value' => Yii::t('app', 'Значение'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
        ];
    }

    /**
     * Fields.
     *
     * @return array
     */
    public function fields()
    {
        return ['_id', 'uuid',
            'equipment' => function ($model) {
                return $model->equipment;
            },
            'operation' => function ($model) {
                return $model->operation;
            },
            'measureType' => function ($model) {
                return $model->measureType;
            }, 'date', 'value', 'createdAt', 'changedAt'
        ];
    }

    /**
     * Объект связанного поля.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEquipment()
    {
        return $this->hasOne(Equipment::className(), ['uuid' => 'equipmentUuid']);
    }

    /**
     * Объект связанного поля.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOperation()
    {
        return $this->hasOne(Operation::className(), ['uuid' => 'operationUuid']);
    }

    /**
     * Объект связанного поля.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMeasureType()
    {
        return $this->hasOne(
            MeasureType::className(), ['uuid' => 'measureTypeUuid']
        );
    }
}
