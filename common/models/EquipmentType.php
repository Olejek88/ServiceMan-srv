<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "equipment_type".
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $title
 * @property string $equipmentSystemUuid
 * @property string $createdAt
 * @property string $changedAt
 *
 * @property EquipmentSystem $equipmentSystem
 */
class EquipmentType extends ActiveRecord
{
    const EQUIPMENT_HVS = '7AB0B720-9FDB-448C-86C1-4649A7FCF279';
    const EQUIPMENT_GVS = '4F50C767-A044-465B-A69F-02DD321BC5FB';
    const EQUIPMENT_ELECTRO = 'B6904443-363B-4F01-B940-F47B463E66D8';
    const EQUIPMENT_HEAT_COUNTER = '42686CFC-34D0-45FF-95A4-04B0D865EC35';

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
        return 'equipment_type';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uuid', 'title'], 'required'],
            [['createdAt', 'changedAt'], 'safe'],
            [['uuid', 'title', 'equipmentSystemUuid'], 'string', 'max' => 45],
        ];
    }

    public function fields()
    {
        return [
            '_id',
            'uuid',
            'title',
            'equipmentSystemUuid',
            'equipmentSystem' => function ($model) {
                return $model->equipmentSystem;
            },
            'createdAt',
            'changedAt',
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
            'title' => Yii::t('app', 'Название'),
            'equipmentSystem' => Yii::t('app', 'Ин.Система'),
            'equipmentSystemUuid' => Yii::t('app', 'Ин.Система'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
        ];
    }
}
