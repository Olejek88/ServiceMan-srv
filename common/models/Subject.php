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
 * @property string $createdAt
 * @property string $changedAt
 *
 * @property House $house
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
            [['uuid', 'houseUuid'], 'required'],
            [['createdAt', 'changedAt', 'contractDate'], 'safe'],
            [['uuid', 'houseUuid', 'contractNumber'], 'string', 'max' => 50],
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

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            '_id' => Yii::t('app', '№'),
            'uuid' => Yii::t('app', 'Uuid'),
            'house' => Yii::t('app', 'Дом'),
            'contractNumber' => Yii::t('app', 'Номер договора'),
            'contractDate' => Yii::t('app', 'Дата договора'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
        ];
    }
}
