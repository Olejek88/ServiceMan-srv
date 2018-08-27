<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\db\ActiveRecord;
/**
 * This is the model class for table "defecttype".
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $equipmentTypeUuid
 * @property string $title
 * @property string $createdAt
 * @property string $changedAt
 */
class DefectType extends ActiveRecord
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
        return 'defect_type';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uuid', 'equipmentTypeUuid', 'title'], 'required'],
            [['createdAt', 'changedAt'], 'safe'],
            [['uuid', 'equipmentTypeUuid'], 'string', 'max' => 45],
            [['title'], 'string', 'max' => 100],
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
            'equipmentTypeUuid' => Yii::t('app', 'Тип оборудования'),
            'title' => Yii::t('app', 'Название'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
        ];
    }

    public function fields()
    {
        return ['_id','uuid',
            'equipmentType' => function ($model) {
                return $model->equipmentType;
            },
            'title',
            'createdAt','changedAt'
        ];
    }

    public function getEquipmentType()
    {
        return $this->hasOne(EquipmentType::className(), ['uuid' => 'equipmentTypeUuid']);
    }

}
