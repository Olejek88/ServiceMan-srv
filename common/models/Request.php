<?php

namespace common\models;

use common\components\IntegrationExtSystem;
use common\components\ZhkhActiveRecord;
use Yii;
use yii\base\InvalidConfigException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\Exception;
use yii\db\Expression;

/**
 * This is the model class for table "request".
 *
 * @property integer $_id
 * @property string $oid идентификатор организации
 * @property string $uuid
 * @property integer $type
 * @property string $contragentUuid
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
 * @property string $extId
 * @property string $integrationClass
 * @property integer $serialNumber
 *
 * @property Contragent $contragent
 * @property Users $author
 * @property RequestStatus $requestStatus
 * @property RequestType $requestType
 * @property Equipment $equipment
 * @property Objects $object
 * @property Task $task
 */
class Request extends ZhkhActiveRecord
{
    public const DESCRIPTION = 'Обращения';

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
                    'contragentUuid',
                    'comment',
                    'requestStatusUuid',
                    'requestTypeUuid',
                    'authorUuid',
                    'equipmentUuid',
                    'comment'
                ],
                'required', 'on' => self::SCENARIO_DEFAULT
            ],
            [
                [
                    'contragentUuid',
                    'equipmentUuid',
                ],
                'safe', 'on' => ZhkhActiveRecord::SCENARIO_API,
            ],
            [['closeDate', 'type', 'createdAt', 'changedAt'], 'safe'],
            [
                [
                    'uuid',
                    'contragentUuid',
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
            [['comment', 'verdict', 'result'], 'string', 'max' => 512],
            [['oid'], 'exist', 'targetClass' => Organization::class, 'targetAttribute' => ['oid' => 'uuid']],
            [['oid'], 'checkOrganizationOwn'],
            [['serialNumber'], 'integer', 'min' => 1],
        ];
    }

    /**
     * Fields
     *
     * @return array
     */
    public function fields()
    {
        $fields = parent::fields();
        return $fields;
//        return ['_id', 'uuid', 'comment',
//            'userCheck',
//            'userUuid',
//            'user' => function ($model) {
//                return $model->user;
//            },
//            'requestStatusUuid',
//            'requestStatus' => function ($model) {
//                return $model->requestStatus;
//            },
//            'requestTypeUuid',
//            'requestType' => function ($model) {
//                return $model->requestType;
//            },
//            'authorUuid',
//            'author' => function ($model) {
//                return $model->author;
//            },
//            'equipmentUuid',
//            'equipment' => function ($model) {
//                return $model->equipment;
//            },
//            'objectUuid',
//            'object' => function ($model) {
//                return $model->object;
//            }, 'closeDate',
//            'taskUuid',
//            'task' => function ($model) {
//                return $model->task;
//            },
//            'createdAt', 'changedAt'
//        ];
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
            'contragentUuid' => Yii::t('app', 'Заявитель'),
            'contragent' => Yii::t('app', 'Заявитель'),
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
            'comment' => Yii::t('app', 'Причина обращения'),
            'serialNumber' => Yii::t('app', '№'),
        ];
    }

    /**
     * Объект связанного поля.
     *
     * @return ActiveQuery
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

    function getActionPermissions()
    {
        return array_merge_recursive(parent::getActionPermissions(), [
            'read' => [
                'info',
                'search',
                'form',
                'history',
                'search-form',
                'messages',
            ],
            'edit' => [
                'new',
                'add-message',
                'save-comment',
            ]]);
    }

    /**
     * @param $request Request
     * @param string $text
     * @return boolean
     * @throws \yii\httpclient\Exception
     * @throws InvalidConfigException
     */
    public static function closeAppeal($request, $text = "")
    {
        if ($request->integrationClass == null || $request->extId == null) {
            return false;
        }

        /** @var IntegrationExtSystem $integrationClass */
        $integrationClass = $request->integrationClass;
        return $integrationClass::closeAppeal($request, $text);
    }

    /**
     * @param $request Request
     * @param $text string Комментарий
     * @return integer
     * @throws \yii\httpclient\Exception
     * @throws InvalidConfigException
     */
    public static function sendComment($request, $text)
    {
        if ($request->integrationClass == null || $request->extId == null) {
            return -1;
        }

        /** @var IntegrationExtSystem $integrationClass */
        $integrationClass = $request->integrationClass;
        return $integrationClass::sendComment($request, $text);
    }

    /**
     * @param bool $insert
     * @return bool
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function beforeSave($insert)
    {
        if ($insert) {
            $this->serialNumber = Request::find()->where(['>=', 'createdAt', date('Y-01-01')])->max('serialNumber') + 1;
        }

        return parent::beforeSave($insert);
    }
}
