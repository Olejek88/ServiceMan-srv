<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "subject".
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $contractNumber
 * @property string $contractDate
 * @property string $houseUuid
 * @property string $flatUuid
 * @property string $createdAt
 * @property string $changedAt
 *
 * @property House $house
 * @property Flat $flat
 */
class Subject extends ActiveRecord
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
        return 'subject';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uuid', 'houseUuid', 'flatUuid'], 'required'],
            [['createdAt', 'changedAt', 'contractDate'], 'safe'],
            [['uuid', 'houseUuid', 'flatUuid', 'contractNumber'], 'string', 'max' => 50],
        ];
    }

    public function fields()
    {
        return [
            '_id',
            'uuid',
            'houseUuid',
            'house' => function ($model) {
                return $model->house;
            },
            'flat' => function ($model) {
                return $model->flat;
            },
            'contractNumber',
            'contractDate',
            'createdAt',
            'changedAt',
        ];
    }

    public function getHouse()
    {
        return $this->hasOne(House::class, ['uuid' => 'houseUuid']);
    }

    public function getFlat()
    {
        return $this->hasOne(Flat::className(), ['uuid' => 'flatUuid']);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            '_id' => Yii::t('app', '№'),
            'uuid' => Yii::t('app', 'Uuid'),
            'house' => Yii::t('app', 'Дом'),
            'flat' => Yii::t('app', 'Помещение'),
            'contractNumber' => Yii::t('app', 'Номер договора'),
            'contractDate' => Yii::t('app', 'Дата договора'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
        ];
    }
}
