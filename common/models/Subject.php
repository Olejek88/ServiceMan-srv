<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "subject".
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $owner
 * @property string $contractNumber
 * @property string $contractDate
 * @property string $houseUuid
 * @property string $flatUuid
 * @property string $createdAt
 * @property string $changedAt
 *
 * @property House $house
 * @property Object $flat
 */
class Subject extends ActiveRecord
{
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
        return 'subject';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uuid', 'houseUuid', 'flatUuid', 'owner'], 'required'],
            [['createdAt', 'changedAt', 'contractDate'], 'safe'],
            [['uuid', 'houseUuid', 'flatUuid', 'contractNumber'], 'string', 'max' => 50],
            [['owner'], 'string', 'max' => 100],
        ];
    }

    public function fields()
    {
        return [
            '_id',
            'uuid',
            'owner',
            'houseUuid',
            'house' => function ($model) {
                return $model->house;
            },
            'flatUuid',
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
        return $this->hasOne(Objects::class, ['uuid' => 'flatUuid']);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            '_id' => Yii::t('app', '№'),
            'uuid' => Yii::t('app', 'Uuid'),
            'owner' => Yii::t('app', 'Субъект'),
            'house' => Yii::t('app', 'Дом'),
            'flat' => Yii::t('app', 'Помещение'),
            'contractNumber' => Yii::t('app', 'Номер договора'),
            'contractDate' => Yii::t('app', 'Дата договора'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
        ];
    }

    public static function getSubjectName($flatUuid)
    {
        $model = Subject::find()->where(["flatUuid" => $flatUuid])->one();
        if(!empty($model)){
            return $model['owner'];
        }
        return null;
    }

}
