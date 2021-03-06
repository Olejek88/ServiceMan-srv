<?php

namespace common\models;

use common\components\ZhkhActiveRecord;
use Yii;
use yii\base\InvalidConfigException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Exception;
use yii\db\Expression;

/**
 * This is the model class for table "operation".
 *
 * @property integer $_id
 * @property string $oid идентификатор организации
 * @property string $uuid
 * @property string $taskUuid
 * @property string $workStatusUuid
 * @property string $operationTemplateUuid
 * @property string $createdAt
 * @property string $changedAt
 *
 * @property Task $task
 * @property OperationTemplate $operationTemplate
 * @property WorkStatus $workStatus
 */
class Operation extends ZhkhActiveRecord
{
    public const DESCRIPTION = 'Операции';

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
        return 'operation';
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
                    'taskUuid',
                    'workStatusUuid',
                    'operationTemplateUuid'
                ],
                'required'
            ],
            [['oid', 'createdAt', 'changedAt'], 'safe'],
            [
                [
                    'uuid',
                    'taskUuid',
                    'workStatusUuid',
                    'operationTemplateUuid'
                ],
                'string', 'max' => 45
            ],
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
        $fields = parent::fields();
        return $fields;
//        return ['_id', 'uuid', 'taskUuid',
//            'workStatusUuid',
//            'workStatus' => function ($model) {
//                return $model->operationStatus;
//            },
//            'operationTemplateUuid',
//            'operationTemplate' => function ($model) {
//                return $model->operationTemplate;
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
            'workStatusUuid' => Yii::t('app', 'Статус'),
            'workStatus' => Yii::t('app', 'Статус'),
            'operationTemplateUuid' => Yii::t('app', 'Шаблон'),
            'operationTemplate' => Yii::t('app', 'Шаблон'),
            'taskUuid' => Yii::t('app', 'Задача'),
            'task' => Yii::t('app', 'Задача'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'изменен'),
        ];
    }

    /**
     * Объект связанного поля.
     *
     * @return ActiveRecord
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function getTask()
    {
        $task = Task::find()
            ->select('*')
            ->where(['uuid' => $this->taskUuid])
            ->one();
        return $task;
    }

    /**
     * Объект связанного поля.
     *
     * @return ActiveQuery
     */
    public function getWorkStatus()
    {
        return $this->hasOne(
            WorkStatus::class, ['uuid' => 'workStatusUuid']
        );
    }

    /**
     * Объект связанного поля.
     *
     * @return ActiveQuery
     */
    public function getOperationTemplate()
    {
        return $this->hasOne(
            OperationTemplate::class, ['uuid' => 'operationTemplateUuid']
        );
    }
}
