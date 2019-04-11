<?php
namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "request".
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $userUuid
 * @property string $requestStatusUuid
 * @property string $comment
 * @property string $equipmentUuid
 * @property string $objectUuid
 * @property string $stageUuid
 * @property string $closeDate
 * @property string $createdAt
 * @property string $changedAt
 *
 * @property Objects $object
 * @property RequestStatus $requestStatus
 * @property Users $user
 * @property Equipment $equipment
 */
class Request extends ToirusModel
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
                    'stageUuid',
                    'objectUuid',
                    'closeDate'
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
            'equipmentUuid',
            'equipment' => function ($model) {
                return $model->equipment;
            },
            'objectUuid',
            'stageUuid',
            'object' => function ($model) {
                return $model->object;
            }, 'closeDate',
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
            'userUuid' => Yii::t('app', 'Uuid пользователя'),
            'user' => Yii::t('app', 'Пользователь'),
            'requestStatusUuid' => Yii::t('app', 'статус заявки'),
            'requestStatus' => Yii::t('app', 'Статус заявки'),
            'equipmentUuid' => Yii::t('app', 'Uuid оборудования'),
            'equipment' => Yii::t('app', 'Оборудование'),
            'objectUuid' => Yii::t('app', 'Uuid объект'),
            'object' => Yii::t('app', 'Объект'),
            'stageUuid' => Yii::t('app', 'Uuid этапа'),
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
}
