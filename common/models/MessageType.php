<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use \yii\db\ActiveRecord;

/**
 * This is the model class for table "message_type".
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $title
 * @property string $createdAt
 * @property string $changedAt
 */
class MessageType extends ActiveRecord
{
    const EVENT_HIGH = '6C17EF9E-6353-417E-8C3C-DFDC5798B809';      // Нештатная ситуация: уровень высокий
    const EVENT_MEDIUM = '2379706D-0107-4875-B2F9-7D348DACBD18';    // Нештатная ситуация: уровень средний
    const EVENT_LOW = '574693EB-B3DA-441C-AA7D-0FD4C4C63CAB';       // Нештатная ситуация: уровень низкий
    const ORDER_CREATED = 'EFE923AA-CD2A-45FD-90DC-DB2A8CF1DE1F';   // Создан наряд
    const ORDER_SEND = '9F1498BF-E104-41A0-B6D5-D8193AA667E1';      // Сдан наряд

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
        return 'message_type';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uuid', 'title'], 'required'],
            [['createdAt', 'changedAt'], 'safe'],
            [['uuid'], 'string', 'max' => 50],
            [['title'], 'string', 'max' => 100],
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
}
