<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "house".
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $number
 * @property string $houseStatusUuid
 * @property string $streetUuid
 * @property string $createdAt
 * @property string $changedAt
 *
 * @property Street $street
 * @property HouseStatus $houseStatus
 * @property PhotoHouse $photoHouse
 */
class House extends ActiveRecord
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
        return 'house';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uuid', 'houseStatusUuid', 'streetUuid'], 'required'],
            [['createdAt', 'changedAt'], 'safe'],
            [['uuid', 'number', 'houseStatusUuid', 'streetUuid'], 'string', 'max' => 50],
        ];
    }

    public function fields()
    {
        return [
            '_id',
            'uuid',
            'number',
            'houseStatusUuid',
            'houseStatus' => function ($model) {
                return $model->houseStatus;
            },
            'streetUuid',
            'street' => function ($model) {
                return $model->street;
            },
            'createdAt',
            'changedAt',
        ];
    }

    public function getHouseStatus()
    {
        return $this->hasOne(HouseStatus::class, ['uuid' => 'houseStatusUuid']);
    }
    public function getStreet()
    {
        return $this->hasOne(Street::class, ['uuid' => 'streetUuid']);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            '_id' => Yii::t('app', '№'),
            'uuid' => Yii::t('app', 'Uuid'),
            'number' => Yii::t('app', 'Номер дома'),
            'houseStatus' => Yii::t('app', 'Статус здания'),
            'street' => Yii::t('app', 'Улица'),
            'houseStatusUuid' => Yii::t('app', 'Статус здания'),
            'streetUuid' => Yii::t('app', 'Улица'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
        ];
    }

    public function getPhotoHouse() {
        return PhotoHouse::hasMany(PhotoHouse::class, ['houseUuid' => 'uuid']);
    }
}
