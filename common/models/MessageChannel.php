<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use \yii\db\ActiveRecord;

/**
 * This is the model class for table "message_channel".
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $title
 * @property string $createdAt
 * @property string $changedAt
 */
class MessageChannel extends ActiveRecord
{
    const CHANNEL_TELEGRAM = 'A6AD14BC-054A-4AB9-88C4-906FC53D13A5';
    const CHANNEL_VIBER =  'BAC55B9B-131E-4DA4-A122-2D05C8484FF6';
    const CHANNEL_SMS = 'C1704378-9C9B-4C1C-9908-6E6E08B5A3A8';
    const CHANNEL_EMAIL = '8E83B37F-EADA-4E4E-900E-1D46F2335D30';
    const CHANNEL_SERVICE = '981E1155-9888-444C-96C9-FCFB6F7699ED';
    const CHANNEL_CHAT = 'AE228926-5358-4A8F-A466-8397DABB9F42';

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
        return 'message_channel';
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
