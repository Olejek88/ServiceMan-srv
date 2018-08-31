<?php
namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "equipment".
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $equipmentTypeUuid
 * @property string $serial
 * @property string $equipmentStatusUuid
 * @property string $testDate
 * @property string $houseUuid
 * @property string $flatUuid
 * @property string $createdAt
 * @property string $changedAt
 */
class Equipment extends ActiveRecord
{

    /**
     * Behaviors.
     *
     * @return array
     */
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
     * Table name.
     *
     * @return string
     */
    public static function tableName()
    {
        return 'equipment';
    }

    /**
     * Свойства объекта со связанными данными.
     *
     * @return array
     */
    public function fields()
    {
        return ['_id', 'uuid',
            'equipmentTypeUuid',
            'equipmentType' => function ($model) {
                return $model->equipmentType;
            },
            'equipmentStatusUuid',
            'equipmentStatus' => function ($model) {
                return $model->equipmentStatus;
            },
            'flatUuid',
            'flat' => function ($model) {
                return $model->flat;
            },
            'serial', 'testDate', 'serial',
            'createdAt', 'changedAt'
        ];
    }

    /**
     * Rules.
     *
     * @return array
     */
    public function rules()
    {
        return [
            [
                [
                    'uuid',
                    'equipmentTypeUuid',
                    'equipmentStatusUuid',
                    'serial',
                ],
                'required'
            ],
            [['testDate', 'createdAt', 'changedAt'], 'safe'],
            [
                [
                    'uuid',
                    'equipmentTypeUuid',
                    'equipmentStatusUuid',
                    'serial',
                    'houseUuid',
                    'flatUuid'
                ],
                'string', 'max' => 50
            ],
        ];
    }

    /**
     * Метки для свойств.
     *
     * @return array
     */
    public function attributeLabels()
    {
        return [
            '_id' => Yii::t('app', '№'),
            'uuid' => Yii::t('app', 'Uuid'),
            'equipmentTypeUuid' => Yii::t('app', 'Тип оборудования'),
            'equipmentType' => Yii::t('app', 'Тип'),
            'testDate' => Yii::t('app', 'Дата последней поверки'),
            'equipmentStatusUuid' => Yii::t('app', 'Статус'),
            'flatUuid' => Yii::t('app', 'Квартира'),
            'houseUuid' => Yii::t('app', 'Дом'),
            'flat' => Yii::t('app', 'Квартира'),
            'house' => Yii::t('app', 'Дом'),
            'serial' => Yii::t('app', 'Серийный номер'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
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
        return $this->hasOne(
            House::className(), ['uuid' => 'houseUuid']
        );
    }

    /**
     * Объект связанного поля.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEquipmentStatus()
    {
        return $this->hasOne(
            EquipmentStatus::className(), ['uuid' => 'equipmentStatusUuid']
        );
    }

    /**
     * Объект связанного поля.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEquipmentType()
    {
        return $this->hasOne(
            EquipmentType::className(), ['uuid' => 'equipmentTypeUuid']
        );
    }

    /**
     * Объект связанного поля.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFlat()
    {
        return $this->hasOne(Flat::className(), ['uuid' => 'flatUuid']);
    }

}
