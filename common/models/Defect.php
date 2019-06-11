<?php

namespace common\models;

use common\components\ZhkhActiveRecord;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\Expression;

/**
 * This is the model class for table "defect".
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $oid
 * @property string $userUuid
 * @property string $title
 * @property string $date
 * @property string $taskUuid
 * @property string $equipmentUuid
 * @property integer $defectStatus
 * @property string $createdAt
 * @property string $changedAt
 *
 * @property Task $task
 * @property Users $user
 * @property ActiveQuery $photo
 * @property Equipment $equipment
 */
class Defect extends ZhkhActiveRecord
{
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
        return 'defect';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uuid', 'date', 'equipmentUuid', 'defectStatus'], 'required'],
            [['oid','createdAt', 'changedAt'], 'safe'],
            [['uuid', 'equipmentUuid', 'userUuid', 'taskUuid'], 'string', 'max' => 45],
            [['title'], 'string', 'max' => 300],
            [['oid'], 'exist', 'targetClass' => Organization::class, 'targetAttribute' => ['oid' => 'uuid']],
            [['oid'], 'checkOrganizationOwn'],
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
            'userUuid' => Yii::t('app', 'Пользователь'),
            'date' => Yii::t('app', 'Дата фиксации'),
            'equipment' => Yii::t('app', 'Оборудование'),
            'equipmentUuid' => Yii::t('app', 'Оборудование'),
            'task' => Yii::t('app', 'Задача'),
            'taskUuid' => Yii::t('app', 'Задача'),
            'defectStatus' => Yii::t('app', 'Статус дефекта'),
            'title' => Yii::t('app', 'Описание'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
        ];
    }

    public function fields()
    {
        return [
            '_id',
            'uuid',
            'user' => function ($model) {
                return $model->user;
            },
            'date',
            'equipment' => function ($model) {
                return $model->equipment;
            },
            'defectStatus',
            'title',
            'task' => function ($model) {
                return $model->task;
            },
            'createdAt',
            'changedAt',
        ];
    }

    public function getEquipment()
    {
        return $this->hasOne(Equipment::class, ['uuid' => 'equipmentUuid']);
    }

    public function getUser()
    {
        return $this->hasOne(Users::class, ['uuid' => 'userUuid']);
    }

    public function getTask()
    {
        return $this->hasOne(Task::class, ['uuid' => 'taskUuid']);
    }

    /**
     * @return ActiveQuery
     */
    public function getPhoto() {
        return $this->hasMany(Photo::class, ['objectUuid' => 'uuid']);
    }

}
