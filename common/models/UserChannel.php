<?php
/**
 * PHP Version 7.0
 *
 * @category Category
 * @package  Common\models
 * @author   Максим Шумаков <ms.profile.d@gmail.com>
 * @license  http://www.yiiframework.com/license/ License name
 * @link     http://www.toirus.ru
 */

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "user_channel".
 *
 * @category Category
 * @package  Common\models
 * @link     http://www.toirus.ru
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $messageChannelUuid
 * @property string $messageTypeUuid
 * @property string $userUuid
 * @property string $channelId
 * @property integer $active
 * @property string $createdAt
 * @property string $changedAt
 */

class UserChannel extends ActiveRecord
{
    /**
     * Behaviors.
     *
     * @return array
     */
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
     * Table name.
     *
     * @return string
     */
    public static function tableName()
    {
        return 'user_channel';
    }

    /**
     * Свойства объекта со связанными данными.
     *
     * @return array
     */
    public function fields()
    {
        return ['_id', 'uuid', 'channelId', 'active',
            'messageChannelUuid' => function ($model) {
                return $model->title;
            },
            'messageTypeUuid' => function ($model) {
                return $model->title;
            },
            'userUuid' => function ($model) {
                return $model->name;
            },
            'createdAt', 'changedAt'
        ];
    }

    /**
     * Rules.
     *
     * @return array
     */
    public function rules()
    {
        return [
            [
                [
                    'uuid',
                    'messageChannelUuid',
                    'messageTypeUuid',
                    'userUuid'
                ],
                'required'
            ],
            [['createdAt', 'changedAt'], 'safe'],
            [
                [
                    'uuid',
                    'messageChannelUuid',
                    'messageTypeUuid',
                    'userUuid'
                ],
                'string', 'max' => 50
            ],
            [['active'], 'integer'],
            [['channelId'], 'string'],
        ];
    }

    /**
     * Метки для свойств.
     *
     * @return array
     */
    public function attributeLabels()
    {
        return [
            '_id' => Yii::t('app', '№'),
            'uuid' => Yii::t('app', 'Uuid'),
            'messageChannelUuid' => Yii::t('app', 'Канал отправки'),
            'messageTypeUuid' => Yii::t('app', 'Тип сообщений'),
            'userUuid' => Yii::t('app', 'Пользователь'),
            'channelId' => Yii::t('app', 'Свойства канала'),
            'active' => Yii::t('app', 'Активность'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
        ];
    }

    /**
     * Проверка целостности модели?
     *
     * @return bool
     */
    public function upload()
    {
        if ($this->validate()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Объект связанного поля.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMessageChannel()
    {
        return $this->hasOne(
            MessageChannel::className(), ['uuid' => 'messageChannelUuid']
        );
    }

    /**
     * Объект связанного поля.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMessageType()
    {
        return $this->hasOne(
            MessageType::className(), ['uuid' => 'messageTypeUuid']
        );
    }

    /**
     * Объект связанного поля.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(
            Users::className(), ['uuid' => 'userUuid']
        );
    }
}
