<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "request_status".
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $title
 * @property string $createdAt
 * @property string $changedAt
 *
 * @property RequestStatus $requestStatus
 */
class RequestStatus extends ActiveRecord
{
    const NEW_REQUEST = "F45775D3-9876-4831-9781-92E00240D44F";
    const IN_WORK = "49085FF9-5223-404A-B98D-7B042BB571A3";
    const COMPLETE = "FB7E8A7C-E228-4226-AAF5-AD3DB472F4ED";
    const UN_COMPLETE = "B17CB2E0-58DF-4CA3-B620-AF8B39D6C229";
    const CANCELED = "8DA302D8-978B-4900-872C-4EB4DE13682A";

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
        return 'request_status';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uuid', 'title'], 'required'],
            [['createdAt', 'changedAt'], 'safe'],
            [['uuid'], 'string', 'max' => 45],
            [['title'], 'string', 'max' => 200],
        ];
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
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRequestStatus()
    {
        return $this->hasOne(RequestStatus::class, ['uuid' => 'requestStatusUuid']);
    }
}
