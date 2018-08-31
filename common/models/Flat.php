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
 */
class Flat extends ActiveRecord
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
            [['uuid', 'flatStatusUuid', 'houseUuid'], 'string', 'max' => 50],
        ];
    }

    public function fields()
    {
        return [
            '_id',
            'uuid',
            'flatStatus' => function ($model) {
                return $model->flatStatus;
            },
            'house' => function ($model) {
                return $model->house;
            },
            'createdAt',
            'changedAt',
        ];
    }

    public function getFlatStatus()
    {
        return $this->hasOne(FlatStatus::className(), ['uuid' => 'flatStatusUuid']);
    }
    public function getHouse()
    {
        return $this->hasOne(House::className(), ['uuid' => 'houseUuid']);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            '_id' => Yii::t('app', '№'),
            'uuid' => Yii::t('app', 'Uuid'),
            'flatStatus' => Yii::t('app', 'Статус квартиры'),
            'house' => Yii::t('app', 'Дом'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
        ];
    }
}
