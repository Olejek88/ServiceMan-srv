<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "resident".
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $owner
 * @property string $inn
 * @property string $flatUuid
 * @property string $createdAt
 * @property string $changedAt
 *
 * @property Object $flat
 */
class Resident extends ActiveRecord
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
        return 'resident';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uuid', 'flatUuid'], 'required'],
            [['createdAt', 'changedAt'], 'safe'],
            [['uuid', 'flatUuid', 'owner', 'inn'], 'string', 'max' => 50],
        ];
    }

    public function fields()
    {
        return [
            '_id',
            'uuid',
            'flatUuid',
            'flat' => function ($model) {
                return $model->flat;
            },
            'owner',
            'inn',
            'createdAt',
            'changedAt',
        ];
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
            'flat' => Yii::t('app', 'Квартира'),
            'flatUuid' => Yii::t('app', 'Квартира'),
            'owner' => Yii::t('app', 'Владелец'),
            'inn' => Yii::t('app', 'ИНН'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
        ];
    }
}
