<?php
namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "request".
 *
 * @property integer $_id
 * @property string $oid идентификатор организации
 * @property string $uuid
 * @property string $userUuid
 * @property string $contragentUuid
 * @property string $authorUuid
 * @property string $requestStatusUuid
 * @property string $requestTypeUuid
 * @property string $comment
 * @property string $equipmentUuid
 * @property string $objectUuid
 * @property string $taskUuid
 * @property string $closeDate
 * @property string $createdAt
 * @property string $changedAt
 *
 * @property Users $user
 * @property Contragent $contragent
 * @property Contragent $author
 * @property RequestStatus $requestStatus
 * @property RequestType $requestType
 * @property Equipment $equipment
 * @property Object $object
 * @property Task $task
 */
class Request extends ActiveRecord
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
        return 'request';
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
                    'userUuid',
                    'comment',
                    'requestStatusUuid',
                    'requestTypeUuid',
                    'contragentUuid',
                    'authorUuid',
                    'equipmentUuid',
                    'objectUuid',
                    'taskUuid',
                    'comment'
                ],
                'required'
            ],
            [['closeDate', 'createdAt', 'changedAt'], 'safe'],
            [
                [
                    'uuid',
                    'userUuid',
                    'requestStatusUuid',
                    'equipmentUuid',
                    'objectUuid',
                    'closeDate',
                    'requestTypeUuid',
                    'contragentUuid',
                    'authorUuid',
                    'taskUuid',
                    'oid',
                ],
                'string',
                'max' => 45
            ],
            [['comment'], 'string', 'max' => 500],
        ];
    }

    /**
     * Fields
     *
     * @return array
     */
    public function fields()
    {
        return ['_id', 'uuid', 'comment',
            'userUuid',
            'user' => function ($model) {
                return $model->user;
            },
            'requestStatusUuid',
            'requestStatus' => function ($model) {
                return $model->requestStatus;
            },
            'requestTypeUuid',
            'requestType' => function ($model) {
                return $model->requestType;
            },
            'contragentUuid',
            'contragent' => function ($model) {
                return $model->contragent;
            },
            'authorUuid',
            'author' => function ($model) {
                return $model->author;
            },
            'equipmentUuid',
            'equipment' => function ($model) {
                return $model->equipment;
            },
            'objectUuid',
            'object' => function ($model) {
                return $model->object;
            }, 'closeDate',
            'taskUuid',
            'task' => function ($model) {
                return $model->task;
            },
            'createdAt', 'changedAt'
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
            'userUuid' => Yii::t('app', 'Пользователь'),
            'user' => Yii::t('app', 'Пользователь'),
            'requestTypeUuid' => Yii::t('app', 'Тип заявки'),
            'requestType' => Yii::t('app', 'Тип заявки'),
            'requestStatusUuid' => Yii::t('app', 'статус заявки'),
            'requestStatus' => Yii::t('app', 'Статус заявки'),
            'equipmentUuid' => Yii::t('app', 'Оборудование'),
            'equipment' => Yii::t('app', 'Оборудование'),
            'objectUuid' => Yii::t('app', 'Объект'),
            'object' => Yii::t('app', 'Объект'),
            'authorUuid' => Yii::t('app', 'Автор заявки'),
            'author' => Yii::t('app', 'Автор заявки'),
            'contragentUuid' => Yii::t('app', 'Исполнитель'),
            'contragent' => Yii::t('app', 'Исполнитель'),
            'taskUuid' => Yii::t('app', 'Задача'),
            'task' => Yii::t('app', 'Задача'),
            'closeDate' => Yii::t('app', 'Дата закрытия заявки'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'изменен'),
            'comment' =>  Yii::t('app', 'Коментарий'),
        ];
    }

    /**
     * Объект связанного поля.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(
            Users::class, ['uuid' => 'userUuid']
        );
    }

    /**
     * Объект связанного поля.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getContragent()
    {
        return $this->hasOne(
            Contragent::class, ['uuid' => 'contragentUuid']
        );
    }

    /**
     * Объект связанного поля.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAuthor()
    {
        return $this->hasOne(
            Contragent::class, ['uuid' => 'authorUuid']
        );
    }

    /**
     * Объект связанного поля.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRequestStatus()
    {
        return $this->hasOne(
            RequestStatus::class, ['uuid' => 'requestStatusUuid']
        );
    }

    /**
     * Объект связанного поля.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRequestType()
    {
        return $this->hasOne(
            RequestType::class, ['uuid' => 'requestTypeUuid']
        );
    }

    /**
     * Объект связанного поля.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEquipment()
    {
        return $this->hasOne(
            Equipment::class, ['uuid' => 'equipmentUuid']
        );
    }

    /**
     * Объект связанного поля.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getObject()
    {
        return $this->hasOne(
            Objects::class, ['uuid' => 'objectUuid']
        );
    }

    /**
     * Объект связанного поля.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTask()
    {
        return $this->hasOne(
            Task::class, ['uuid' => 'taskUuid']
        );
    }
}
