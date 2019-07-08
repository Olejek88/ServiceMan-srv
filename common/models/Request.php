<?php
namespace common\models;

use common\components\ZhkhActiveRecord;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\Expression;

/**
 * This is the model class for table "request".
 *
 * @property integer $_id
 * @property string $oid идентификатор организации
 * @property string $uuid
 * @property integer $type
 * @property string $userUuid
 * @property string $authorUuid
 * @property string $requestStatusUuid
 * @property string $requestTypeUuid
 * @property string $comment
 * @property string $verdict
 * @property string $result
 * @property string $equipmentUuid
 * @property string $objectUuid
 * @property string $taskUuid
 * @property string $closeDate
 * @property string $createdAt
 * @property string $changedAt
 *
 * @property Users $user
 * @property Contragent $author
 * @property RequestStatus $requestStatus
 * @property RequestType $requestType
 * @property Equipment $equipment
 * @property Object $object
 * @property Task $task
 */
class Request extends ZhkhActiveRecord
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
                    'authorUuid',
                    'equipmentUuid',
                    'comment'
                ],
                'required'
            ],
            [['closeDate', 'type', 'createdAt', 'changedAt'], 'safe'],
            [
                [
                    'uuid',
                    'userUuid',
                    'requestStatusUuid',
                    'equipmentUuid',
                    'objectUuid',
                    'closeDate',
                    'requestTypeUuid',
                    'authorUuid',
                    'taskUuid',
                    'oid',
                ],
                'string',
                'max' => 45
            ],
            [['comment', 'verdict', 'result'], 'string', 'max' => 500],
            [['oid'], 'exist', 'targetClass' => Organization::class, 'targetAttribute' => ['oid' => 'uuid']],
            [['oid'], 'checkOrganizationOwn'],
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
            'userCheck',
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
            'userUuid' => Yii::t('app', 'Заявитель'),
            'user' => Yii::t('app', 'Заявитель'),
            'type' => Yii::t('app', 'Тип'),
            'requestTypeUuid' => Yii::t('app', 'Характер обращения'),
            'requestType' => Yii::t('app', 'Характер обращения'),
            'requestStatusUuid' => Yii::t('app', 'статус заявки'),
            'requestStatus' => Yii::t('app', 'Статус заявки'),
            'equipmentUuid' => Yii::t('app', 'Элемент'),
            'equipment' => Yii::t('app', 'Элемент'),
            'objectUuid' => Yii::t('app', 'Адрес'),
            'object' => Yii::t('app', 'Адрес'),
            'authorUuid' => Yii::t('app', 'Автор заявки'),
            'author' => Yii::t('app', 'Автор заявки'),
            'taskUuid' => Yii::t('app', 'Задача'),
            'task' => Yii::t('app', 'Задача'),
            'closeDate' => Yii::t('app', 'Дата закрытия заявки'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'изменен'),
            'comment' =>  Yii::t('app', 'Причина обращения'),
        ];
    }

    /**
     * Объект связанного поля.
     *
     * @return ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(
            Contragent::class, ['uuid' => 'userUuid']
        );
    }

    /**
     * Объект связанного поля.
     *
     * @return ActiveQuery
     */
    public function getAuthor()
    {
        return $this->hasOne(
            Users::class, ['uuid' => 'authorUuid']
        );
    }

    /**
     * Объект связанного поля.
     *
     * @return ActiveQuery
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
     * @return ActiveQuery
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
     * @return ActiveQuery
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
     * @return ActiveQuery
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
     * @return ActiveQuery
     */
    public function getTask()
    {
        return $this->hasOne(
            Task::class, ['uuid' => 'taskUuid']
        );
    }
}
