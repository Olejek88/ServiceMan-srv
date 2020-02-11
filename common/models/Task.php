<?php

namespace common\models;

use api\controllers\IntegrationIsController;
use common\components\ZhkhActiveRecord;
use Yii;
use yii\base\InvalidConfigException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Exception;
use yii\db\Expression;
use yii\helpers\Html;

/**
 * This is the model class for table "task".
 *
 * @property integer $_id
 * @property string $oid идентификатор организации
 * @property string $uuid
 * @property string $comment
 * @property string $workStatusUuid
 * @property string $authorUuid
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
 * @property Users $author
 * @property Request $request
 * @property WorkStatus $workStatus
 * @property Equipment $equipment
 * @property Operation $operations
 */
class Task extends ZhkhActiveRecord
{
    public const DESCRIPTION = 'Задачи';

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
            [['startDate', 'taskDate', 'authorUuid', 'deadlineDate', 'endDate', 'createdAt', 'changedAt'], 'safe'],
            [['uuid', 'workStatusUuid', 'authorUuid', 'taskVerdictUuid', 'taskTemplateUuid', 'equipmentUuid', 'oid'], 'string', 'max' => 45],
            [['oid'], 'exist', 'targetClass' => Organization::class, 'targetAttribute' => ['oid' => 'uuid']],
            [['oid'], 'checkOrganizationOwn'],
        ];
    }

    public function fields()
    {
        $fields = parent::fields();
        return $fields;
//        return ['_id','uuid', 'comment',
//            'workStatusUuid',
//            'workStatus' => function ($model) {
//                return $model->workStatus;
//            },
//            'taskVerdictUuid',
//            'taskVerdict' => function ($model) {
//                return $model->taskVerdict;
//            },
//            'taskTemplateUuid',
//            'taskTemplate' => function ($model) {
//                return $model->taskTemplate;
//            },
//            'equipmentUuid',
//            'comment',
//            'equipment' => function ($model) {
//                return $model->equipment;
//            },
//            'author' => function ($model) {
//                return $model->author;
//            },
//            'startDate', 'authorUuid', 'deadlineDate', 'endDate', 'createdAt', 'changedAt',
//            'operations' => function ($model) {
//                return $model->operations;
//            },
//        ];
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
            'authorUuid' => Yii::t('app', 'Автор'),
            'author' => Yii::t('app', 'Автор'),
            'equipmentUuid' => Yii::t('app', 'Оборудование'),
            'equipment' => Yii::t('app', 'Оборудование'),
            'workStatusUuid' => Yii::t('app', 'Статус'),
            'workStatus' => Yii::t('app', 'Статус'),
            'taskVerdictUuid' => Yii::t('app', 'Вердикт'),
            'taskVerdict' => Yii::t('app', 'Вердикт'),
            'taskTemplateUuid' => Yii::t('app', 'Шаблон'),
            'taskTemplate' => Yii::t('app', 'Шаблон'),
            'taskDate' => Yii::t('app', 'Дата начала работ'),
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

    public function getAuthor()
    {
        return $this->hasOne(Users::class, ['uuid' => 'authorUuid']);
    }

    public function getWorkStatus()
    {
        return $this->hasOne(WorkStatus::class, ['uuid' => 'workStatusUuid']);
    }

    /**
     * @return bool
     * @throws Exception
     * @throws InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    public function setWorkStatus()
    {
        $change_status = 0;
        $setting = Settings::find()->where(['uuid' => Settings::SETTING_REQUEST_STATUS_FROM_TASK])->one();
        if ($setting && $setting['parameter'] == "1")
            $change_status = 1;
        $request = Request::find()->where(['taskUuid' => $this->uuid])->one();
        /** @var Request $request */
        if ($request && $request->requestStatusUuid != RequestStatus::COMPLETE) {
            if ($change_status) {
                IntegrationIsController::closeAppeal($request->oid, $request->extId, "Задача по этому запросу выполнена");
                $request->requestStatusUuid = RequestStatus::COMPLETE;
                $request->result = "Заявка выполнена";
                $request->save();
                return true;
            }
        }
        return false;
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

    /**
     * @return array|ActiveRecord[]
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function getUsers()
    {
        $taskUsers = TaskUser::find()->select('userUuid')->where(['taskUuid' => $this->uuid])->all();
        $taskUserList = [];
        foreach ($taskUsers as $taskUser) {
            $taskUserList[] = $taskUser['userUuid'];
        }
        $users = Users::find()->where(['IN', 'uuid', $taskUserList])->all();
        return $users;
    }

    /**
     * @return string
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function getRequest()
    {
        $request = Request::find()->where(['taskUuid' => $this->uuid])->one();
        if ($request) {
            $name = "<span class='badge' style='background-color: lightblue; height: 22px'>Заявка #" . $request['_id'] . "</span>";
            $link = Html::a($name, ['../request/index', 'uuid' => $request['uuid']], ['title' => 'Заявка']);
            return $link;
        } else
            return "без заявки";
    }

    function getActionPermissions()
    {
        return array_merge_recursive(parent::getActionPermissions(), [
            'read' => [
                'table-user',
                'table-user-normative',
                'table-report-view',
                'table',
                'search',
                'report',
                'tree',
                'form',
                'add-periodic',
                'user',
                'info',
                'calendar',
                'defects',
                'measures',
                'photos',
                'refresh',
            ],
            'edit' => [
                'add-task',
                'new',
                'name',
                'new-periodic',
                'remove',
            ]]);
    }
}
