<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\db\ActiveRecord;
/**
 * This is the model class for table "equipment_attribute".
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $attributeTypeUuid
 * @property string $equipmentUuid
 * @property string $date
 * @property string $value
 * @property string $createdAt
 * @property string $changedAt
 */
class EquipmentAttribute extends ActiveRecord
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
        return 'equipment_attribute';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uuid'], 'required'],
            [['createdAt', 'changedAt'], 'safe'],
            [['uuid','attributeTypeUuid','equipmentUuid', 'date'], 'string', 'max' => 50],
            [['value'], 'string', 'max' => 100],
        ];
    }

    /**
     * Свойства объекта со связанными данными.
     *
     * @return array
     */
    public function fields()
    {
        return ['_id', 'uuid',
            'attributeTypeUuid',
            'attributeType' => function ($model) {
                return $model->attributeType;
            },
            'equipmentUuid',
            'equipment' => function ($model) {
                return $model->equipment;
            },
            'date', 'value',
            'createdAt', 'changedAt'
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            '_id' => Yii::t('app', '№'),
            'uuid' => Yii::t('app', 'Uuid'),
            'attributeTypeUuid' => Yii::t('app', 'Тип аттрибута'),
            'equipmentUuid' => Yii::t('app', 'Оборудование'),
            'date' => Yii::t('app', 'Дата'),
            'value' => Yii::t('app', 'Значение аттрибута'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
        ];
    }

    /**
     * Проверка целостности модели
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
    public function getAttributeType()
    {
        return $this->hasOne(
            AttributeType::className(), ['uuid' => 'attributeTypeUuid']
        );
    }

    /**
     * Объект связанного поля.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEquipment()
    {
        return $this->hasOne(
            Equipment::className(), ['uuid' => 'equipmentUuid']
        );
    }
}
