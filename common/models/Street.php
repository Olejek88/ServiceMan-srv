<?php

namespace common\models;

use common\components\ZhkhActiveRecord;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\Expression;

/**
 * This is the model class for table "street".
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $oid идентификатор организации
 * @property string $gis_id глобальный идентификатор в ГИС ЖКХ
 * @property string $title
 * @property string $cityUuid
 * @property boolean $deleted
 * @property string $createdAt
 * @property string $changedAt
 *
 * @property House[] $houses
 * @property City $city
 */
class Street extends ZhkhActiveRecord
{
    public const DESCRIPTION = 'Улицы';

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
        return 'street';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uuid', 'title', 'cityUuid'], 'required'],
            [['createdAt', 'changedAt'], 'safe'],
            [['deleted'], 'boolean'],
            [['uuid', 'title', 'cityUuid', 'oid'], 'string', 'max' => 50],
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
//            'cityUuid',
//            'city' => function ($model) {
//                return $model->city;
//            },
//            'title',
//            'createdAt',
//            'changedAt',
//        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getCity()
    {
        return $this->hasOne(City::class, ['uuid' => 'cityUuid']);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            '_id' => Yii::t('app', '№'),
            'uuid' => Yii::t('app', 'Uuid'),
            'title' => Yii::t('app', 'Улица'),
            'city' => Yii::t('app', 'Город'),
            'cityUuid' => Yii::t('app', 'Город'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getHouses()
    {
        return $this->hasMany(House::class, ['streetUuid' => 'uuid'])->where(['house.deleted' => false]);
    }
}
