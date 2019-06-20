<?php

namespace common\models;

use common\components\ZhkhActiveRecord;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
/**
 * This is the model class for table "task".
 *
 * @property integer $_id
 * @property string $oid идентификатор организации
 * @property string $uuid
 * @property string $comment
 * @property string $workStatusUuid
 * @property string $equipmentUuid
 * @property string $taskVerdictUuid
 * @property string $taskTemplateUuid
 * @property string $taskDate
 * @property string $startDate
 * @property string $deadlineDate
 * @property string $endDate
 * @property string $createdAt
 * @property string $changedAt
 *
 * @property TaskVerdict $taskVerdict
 * @property TaskTemplate $taskTemplate
 * @property Users $users
 * @property WorkStatus $workStatus
 * @property Equipment $equipment
 * @property Operation $operations
 */
class Task extends ZhkhActiveRecord
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
        return 'task';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uuid', 'workStatusUuid'], 'required'],
            [['comment'], 'string'],
            [['startDate', 'taskDate', 'deadlineDate', 'endDate', 'createdAt', 'changedAt'], 'safe'],
            [['uuid', 'workStatusUuid', 'taskVerdictUuid', 'taskTemplateUuid', 'equipmentUuid', 'oid'], 'string', 'max' => 45],
            [['oid'], 'exist', 'targetClass' => Organization::class, 'targetAttribute' => ['oid' => 'uuid']],
            [['oid'], 'checkOrganizationOwn'],
        ];
    }

    public function fields()
    {
        return ['_id','uuid', 'comment',
            'workStatusUuid',
            'workStatus' => function ($model) {
                return $model->workStatus;
            },
            'taskVerdictUuid',
            'taskVerdict' => function ($model) {
                return $model->taskVerdict;
            },
            'taskTemplateUuid',
            'taskTemplate' => function ($model) {
                return $model->taskTemplate;
            },
            'equipmentUuid',
            'comment',
            'equipment' => function ($model) {
                return $model->equipment;
            },
            'startDate', 'deadlineDate', 'endDate', 'createdAt', 'changedAt',
            'operations' => function ($model) {
                return $model->operations;
            },
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
            'comment' => Yii::t('app', 'Комментарий'),
            'equipmentUuid' => Yii::t('app', 'Оборудование'),
            'equipment' => Yii::t('app', 'Оборудование'),
            'workStatusUuid' => Yii::t('app', 'Статус'),
            'workStatus' => Yii::t('app', 'Статус'),
            'taskVerdictUuid' => Yii::t('app', 'Вердикт'),
            'taskVerdict' => Yii::t('app', 'Вердикт'),
            'taskTemplateUuid' => Yii::t('app', 'Шаблон'),
            'taskTemplate' => Yii::t('app', 'Шаблон'),
            'taskDate' => Yii::t('app', 'Назначена'),
            'deadlineDate' => Yii::t('app', 'Срок'),
            'startDate' => Yii::t('app', 'Начало'),
            'endDate' => Yii::t('app', 'Окончание'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
        ];
    }

    public function getTaskVerdict()
    {
        return $this->hasOne(TaskVerdict::class, ['uuid' => 'taskVerdictUuid']);
    }

    public function getTaskTemplate()
    {
        return $this->hasOne(TaskTemplate::class, ['uuid' => 'taskTemplateUuid']);
    }

    public function getWorkStatus()
    {
        return $this->hasOne(WorkStatus::class, ['uuid' => 'workStatusUuid']);
    }

    public function getEquipment()
    {
        return $this->hasOne(
            Equipment::class, ['uuid' => 'equipmentUuid']
        );
    }

    public function getOperations()
    {
        return $this->hasMany(Operation::class, ['taskUuid' => 'uuid']);
    }

    public function getUsers()
    {
        $taskUsers = TaskUser::find()->select('userUuid')->where(['taskUuid' => $this->uuid])->all();
        $taskUserList = [];
        foreach ($taskUsers as $taskUser) {
            $taskUserList[]=$taskUser['userUuid'];
        }
        $users = Users::find()->where(['IN','uuid', $taskUserList])->all();
        return $users;
    }
}
