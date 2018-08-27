<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\db\ActiveRecord;
/**
 * This is the model class for table "objects_attribute".
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $attributeTypeUuid
 * @property string $objectUuid
 * @property string $date
 * @property string $value
 * @property string $createdAt
 * @property string $changedAt
 */
class ObjectsAttribute extends ActiveRecord
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
        return 'objects_attribute';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uuid'], 'required'],
            [['createdAt', 'changedAt'], 'safe'],
            [['uuid','attributeTypeUuid','objectUuid', 'date'], 'string', 'max' => 50],
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
            'objectUuid',
            'object' => function ($model) {
                return $model->object;
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
            'objectUuid' => Yii::t('app', 'Объект'),
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
    public function getObject()
    {
        return $this->hasOne(
            Objects::className(), ['uuid' => 'objectUuid']
        );
    }
}
