<?php

namespace common\models;

use common\components\ZhkhActiveRecord;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
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
 * @property double $latitude
 * @property double $longitude
 * @property string $createdAt
 * @property string $changedAt
 * @property string $houseTypeUuid
 * @property boolean $deleted
 *
 * @property Street $street
 * @property HouseStatus $houseStatus
 * @property Photo $photo
 * @property string $fullTitle
 * @property HouseType $houseType
 * @property Objects[] $objects
 */
class House extends ZhkhActiveRecord
{
    public const DESCRIPTION = 'Дома';

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
            [['uuid', 'houseTypeUuid',  'houseStatusUuid', 'streetUuid'], 'required'],
            [['createdAt', 'changedAt', 'deleted'], 'safe'],
            [['uuid', 'number', 'houseStatusUuid', 'houseTypeUuid', 'streetUuid', 'oid'], 'string', 'max' => 50],
            [['latitude', 'longitude'], 'number'],
            [['houseStatusUuid'], 'exist', 'targetClass' => HouseStatus::class, 'targetAttribute' => ['houseStatusUuid' => 'uuid']],
            [['streetUuid'], 'exist', 'targetClass' => Street::class, 'targetAttribute' => ['streetUuid' => 'uuid']],
            [['houseTypeUuid'], 'exist', 'targetClass' => HouseType::class, 'targetAttribute' => ['houseTypeUuid' => 'uuid']],
            [['oid'], 'exist', 'targetClass' => Organization::class, 'targetAttribute' => ['oid' => 'uuid']],
            [['oid'], 'checkOrganizationOwn'],
        ];
    }

    public function fields()
    {
        $fields = parent::fields();
        return $fields;
//        return [
//            '_id',
//            'uuid',
//            'number',
//            'longitude',
//            'latitude',
//            'houseStatusUuid',
//            'houseStatus' => function ($model) {
//                return $model->houseStatus;
//            },
//            'houseTypeUuid',
//            'houseType' => function ($model) {
//                return $model->houseType;
//            },
//            'streetUuid',
//            'street' => function ($model) {
//                return $model->street;
//            },
//            'createdAt',
//            'changedAt',
//        ];
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
            'latitude' => Yii::t('app', 'Широта'),
            'longitude' => Yii::t('app', 'Долгота'),
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
     * @return ActiveQuery
     */
    public function getPhoto()
    {
        return $this->hasMany(Photo::class, ['houseUuid' => 'uuid']);
    }

    /**
     * @return ActiveQuery
     */
    public function getHouseType()
    {
        return $this->hasOne(HouseType::class, ['uuid' => 'houseTypeUuid']);
    }

    public function getFullTitle()
    {
        return 'ул.' . $this->street['title'] . ', д.' . $this->number;
    }

    /**
     * @return ActiveQuery
     */
    public function getObjects()
    {
        return $this->hasMany(Objects::class, ['houseUuid' => 'uuid'])->where(['object.deleted' => false]);
    }

}
