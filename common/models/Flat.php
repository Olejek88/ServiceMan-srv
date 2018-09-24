<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "flat".
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $number
 * @property string $flatStatusUuid
 * @property string $houseUuid
 * @property string $createdAt
 * @property string $changedAt
 * @property string $flatTypeUuid
 *
 * @property House $house
 * @property FlatStatus $flatStatus
 * @property PhotoFlat $photoFlat
 * @property FlatType $flatType
 */
class Flat extends ActiveRecord
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
        return 'flat';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uuid', 'flatStatusUuid', 'houseUuid'], 'required'],
            [['createdAt', 'changedAt'], 'safe'],
            [['uuid', 'number', 'flatStatusUuid', 'houseUuid'], 'string', 'max' => 50],
        ];
    }

    public function fields()
    {
        return [
            '_id',
            'uuid',
            'number',
            'flatStatusUuid',
            'flatStatus' => function ($model) {
                return $model->flatStatus;
            },
            'flatTypeUuid',
            'flatType' => function($model) {
                return $model->flatType;
            },
            'houseUuid',
            'house' => function ($model) {
                return $model->house;
            },
            'createdAt',
            'changedAt',
        ];
    }

    public function getFlatStatus()
    {
        return $this->hasOne(FlatStatus::class, ['uuid' => 'flatStatusUuid']);
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
            'number' => Yii::t('app', 'Номер квартиры'),
            'flatStatusUuid' => Yii::t('app', 'Статус квартиры'),
            'flatStatus' => Yii::t('app', 'Статус квартиры'),
            'houseUuid' => Yii::t('app', 'Дом'),
            'house' => Yii::t('app', 'Дом'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPhotoFlat() {
        return $this->hasMany(PhotoFlat::class, ['flatUuid' => 'uuid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFlatType() {
        return $this->hasOne(FlatType::class, ['uuid' => 'flatTypeUuid']);
    }
}
