<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "house".
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $oid идентификатор организации
 * @property string $gis_id глобальный идентификатор в ГИС ЖКХ
 * @property string $number
 * @property string $houseStatusUuid
 * @property string $streetUuid
 * @property string $createdAt
 * @property string $changedAt
 * @property string $houseTypeUuid
 * @property boolean $deleted
 *
 * @property Street $street
 * @property HouseStatus $houseStatus
 * @property Photo $photo
 * @property HouseType $houseType
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
            [['createdAt', 'changedAt', 'deleted'], 'safe'],
            [['uuid', 'number', 'houseStatusUuid', 'houseTypeUuid', 'streetUuid', 'oid'], 'string', 'max' => 50],
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
            'houseTypeUuid',
            'houseType' => function ($model) {
                return $model->houseType;
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
            'houseTypeUuid' => Yii::t('app', 'Тип дома'),
            'houseType' => Yii::t('app', 'Тип дома'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPhoto()
    {
        return $this->hasMany(Photo::class, ['houseUuid' => 'uuid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHouseType()
    {
        return $this->hasOne(HouseType::class, ['uuid' => 'houseTypeUuid']);
    }

    public function getFullTitle() {
        return 'ул.'.$this->street['title'].', д.'.$this->number;
    }
}
