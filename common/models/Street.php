<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "street".
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $title
 * @property string $cityUuid
 * @property string $createdAt
 * @property string $changedAt
 *
 * @property City $city
 */
class Street extends ActiveRecord
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
            [['uuid', 'title', 'cityUuid'], 'string', 'max' => 50],
        ];
    }

    public function fields()
    {
        return [
            '_id',
            'uuid',
            'cityUuid',
            'city' => function ($model) {
                return $model->city;
            },
            'title',
            'createdAt',
            'changedAt',
        ];
    }

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
}
