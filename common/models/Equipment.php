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
 * @property string $oid идентификатор организации
 * @property string $title
 * @property string $equipmentTypeUuid
 * @property string $serial
 * @property string $tag
 * @property string $equipmentStatusUuid
 * @property string $testDate
 * @property string $objectUuid
 * @property string $createdAt
 * @property string $changedAt
 * @property boolean $deleted
 *
 * @property EquipmentStatus $equipmentStatus
 * @property EquipmentType $equipmentType
 * @property Object $object
 * @property Photo $photo
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
                'class' => TimestampBehavior::class,
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
        return ['_id', 'uuid', 'title',
            'objectUuid',
            'object' => function ($model) {
                return $model->object;
            },
            'equipmentTypeUuid',
            'equipmentType' => function ($model) {
                return $model->equipmentType;
            },
            'equipmentStatusUuid',
            'equipmentStatus' => function ($model) {
                return $model->equipmentStatus;
            },
            'serial', 'testDate', 'tag', 'deleted',
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
                    'title',
                    'equipmentTypeUuid',
                    'equipmentStatusUuid',
                    'serial',
                ],
                'required'
            ],
            [['testDate', 'createdAt', 'changedAt'], 'safe'],
            [['deleted'], 'boolean'],
            [
                [
                    'uuid',
                    'equipmentTypeUuid',
                    'equipmentStatusUuid',
                    'serial',
                    'tag',
                    'oid',
                    'objectUuid'
                ],
                'string', 'max' => 50
            ],
            [
                [
                    'title'
                ],
                'string', 'max' => 150
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
            'title' => Yii::t('app', 'Название'),
            'equipmentTypeUuid' => Yii::t('app', 'Тип оборудования'),
            'equipmentType' => Yii::t('app', 'Тип'),
            'testDate' => Yii::t('app', 'Дата последней поверки'),
            'equipmentStatusUuid' => Yii::t('app', 'Статус'),
            'equipmentStatus' => Yii::t('app', 'Статус'),
            'objectUuid' => Yii::t('app', 'Объект'),
            'object' => Yii::t('app', 'Объект'),
            'tag' => Yii::t('app', 'Метка'),
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
    public function getEquipmentStatus()
    {
        return $this->hasOne(
            EquipmentStatus::class, ['uuid' => 'equipmentStatusUuid']
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
            EquipmentType::class, ['uuid' => 'equipmentTypeUuid']
        );
    }

    /**
     * Объект связанного поля.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getObject()
    {
        return $this->hasOne(Objects::class, ['uuid' => 'objectUuid']);
    }

    public function getPhoto() {
        return $this->hasMany(Photo::class, ['equipmentUuid' => 'uuid']);
    }
}
