<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "message".
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $flatUuid
 * @property string $userUuid
 * @property string $date
 * @property string $createdAt
 * @property string $changedAt
 * @property string $message
 *
 * @property Users $user
 * @property Flat $flat
 */
class Message extends ActiveRecord
{

    /**
     * Behaviors
     *
     * @return array
     */
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
     * Название таблицы
     *
     * @inheritdoc
     *
     * @return string
     */
    public static function tableName()
    {
        return '{{%message}}';
    }

    /**
     * Rules
     *
     * @inheritdoc
     *
     * @return array
     */
    public function rules()
    {
        return [
            [
                [
                    'uuid',
                    'flatUuid',
                    'userUuid',
                    'date'
                ],
                'required'
            ],
            [['uuid', 'flatUuid', 'userUuid', 'date'], 'string', 'max' => 50],
            [['createdAt', 'changedAt'], 'safe'],
        ];
    }

    /**
     * Названия отрибутов
     *
     * @inheritdoc
     *
     * @return array
     */
    public function attributeLabels()
    {
        return [
            '_id' => Yii::t('app', '№'),
            'uuid' => Yii::t('app', 'Uuid'),
            'flatUuid' => Yii::t('app', 'Квартира'),
            'userUuid' => Yii::t('app', 'Пользователь'),
            'flat' => Yii::t('app', 'Квартира'),
            'user' => Yii::t('app', 'Пользователь'),
            'date' => Yii::t('app', 'Дата'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
            'message' => Yii::t('app', 'Сообщение'),
        ];
    }

    /**
     * Fields
     *
     * @return array
     */
    public function fields()
    {
        return ['_id', 'uuid',
            'flatUuid',
            'flat' => function ($model) {
                return $model->flat;
            },
            'userUuid',
            'user' => function ($model) {
                return $model->user;
            },
            'date',
            'createdAt',
            'changedAt',
            'message',
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
    public function getFlat()
    {
        return $this->hasOne(Flat::class, ['uuid' => 'flatUuid']);
    }

    /**
     * Объект связанного поля.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Users::class, ['uuid' => 'userUuid']);
    }

    public static function getLastMessage($flatUuid)
    {
        $model = Message::find()->where(["flatUuid" => $flatUuid])->orderBy('date DESC')->one();
        if(!empty($model)){
            return $model['message'];
        }
        return null;
    }
}
