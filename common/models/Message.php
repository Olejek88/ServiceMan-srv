<?php

namespace common\models;

use common\components\ZhkhActiveRecord;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "messages".
 *
 * @property integer $_id
 * @property string $oid идентификатор организации
 * @property string $uuid
 * @property string $fromUserUuid
 * @property string $toUserUuid
 * @property string $text
 * @property string $date
 * @property integer $status
 * @property string $createdAt
 * @property string $changedAt
 *
 * @property Users $fromUser
 * @property Users $toUser
 */
class Message extends ZhkhActiveRecord
{
    const MESSAGE_NEW = 0;
    const MESSAGE_READ = 1;
    const MESSAGE_DELETED = 2;

    /**
     * Behaviors.
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
     * Table name.
     *
     * @return string
     */
    public static function tableName()
    {
        return 'messages';
    }

    /**
     * Свойства объекта со связанными данными.
     *
     * @return array
     */
    public function fields()
    {
        $fields = parent::fields();
        return $fields;
//        return ['_id', 'uuid', 'date', 'text', 'status',
//            'fromUserUuid' => function ($model) {
//                return $model->name;
//            },
//            'toUserUuid' => function ($model) {
//                return $model->name;
//            },
//            'createdAt', 'changedAt'
//        ];
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
                    'fromUserUuid',
                    'toUserUuid',
                    'text',
                    'status',
                    'date'
                ],
                'required'
            ],
            [['createdAt', 'changedAt'], 'safe'],
            [
                [
                    'uuid',
                    'fromUserUuid',
                    'toUserUuid',
                    'oid',
                    'date'
                ],
                'string', 'max' => 50
            ],
            [['status'], 'integer'],
            [['text'], 'string'],
            [['oid'], 'exist', 'targetClass' => Organization::class, 'targetAttribute' => ['oid' => 'uuid']],
            [['oid'], 'checkOrganizationOwn'],
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
            'fromUserUuid' => Yii::t('app', 'Отправитель'),
            'toUserUuid' => Yii::t('app', 'Получатель'),
            'date' => Yii::t('app', 'Дата'),
            'status' => Yii::t('app', 'Статус'),
            'text' => Yii::t('app', 'Текст'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
        ];
    }

    /**
     * Объект связанного поля.
     *
     * @return ActiveQuery
     */
    public function getFromUser()
    {
        return $this->hasOne(
            Users::class, ['uuid' => 'fromUserUuid']
        );
    }

    /**
     * Объект связанного поля.
     *
     * @return ActiveQuery
     */
    public function getToUser()
    {
        return $this->hasOne(
            Users::class, ['uuid' => 'toUserUuid']
        );
    }
}
