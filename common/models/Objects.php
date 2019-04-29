<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "object".
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $oid идентификатор организации
 * @property string $gis_id глобальный идентификатор в ГИС ЖКХ
 * @property string $title
 * @property string $objectStatusUuid
 * @property string $houseUuid
 * @property string $createdAt
 * @property string $changedAt
 * @property string $objectTypeUuid
 * @property boolean $deleted
 *
 * @property House $house
 * @property ObjectStatus $objectStatus
 * @property Photo $photo
 * @property ObjectType $objectType
 */
class Objects extends ActiveRecord
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
        return 'object';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uuid', 'objectStatusUuid', 'objectTypeUuid', 'houseUuid'], 'required'],
            [['createdAt', 'changedAt'], 'safe'],
            [['deleted'], 'boolean'],
            [['uuid', 'title', 'objectStatusUuid', 'objectTypeUuid', 'houseUuid', 'oid'], 'string', 'max' => 50],
        ];
    }

    public function fields()
    {
        return [
            '_id',
            'uuid',
            'title',
            'objectStatusUuid',
            'objectStatus' => function ($model) {
                return $model->objectStatus;
            },
            'objectTypeUuid',
            'objectType' => function($model) {
                return $model->objectType;
            },
            'houseUuid',
            'house' => function ($model) {
                return $model->house;
            },
            'createdAt',
            'changedAt',
        ];
    }

    public function getObjectStatus()
    {
        return $this->hasOne(ObjectStatus::class, ['uuid' => 'objectStatusUuid']);
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
            'title' => Yii::t('app', 'Название'),
            'objectStatusUuid' => Yii::t('app', 'Статус объекта'),
            'objectStatus' => Yii::t('app', 'Статус объекта'),
            'objectTypeUuid' => Yii::t('app', 'Тип объекта'),
            'objectType' => Yii::t('app', 'Тип объекта'),
            'houseUuid' => Yii::t('app', 'Дом'),
            'house' => Yii::t('app', 'Дом'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPhoto() {
        return $this->hasMany(Photo::class, ['objectUuid' => 'uuid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getObjectType() {
        return $this->hasOne(ObjectType::class, ['uuid' => 'objectTypeUuid']);
    }

    public function getFullTitle() {
        return 'ул.'.$this->house['street']['title'].', д.'.$this->house['number'];
    }

}
