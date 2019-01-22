<?php
namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "operation".
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $stageUuid
 * @property string $workStatusUuid
 * @property string $operationTemplateUuid
 * @property string $startDate
 * @property string $endDate
 * @property string $createdAt
 * @property string $changedAt
 */
class Operation extends ActiveRecord
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
                    'taskStatusUuid',
                    'operationTemplateUuid'
                ],
                'required'
            ],
            [['startDate', 'endDate', 'createdAt', 'changedAt'], 'safe'],
            [
                [
                    'uuid',
                    'taskUuid',
                    'workStatusUuid',
                    'operationTemplateUuid'
                ],
                'string', 'max' => 45
            ],
        ];
    }

    /**
     * Fields
     *
     * @return array
     */
    public function fields()
    {
        return ['_id', 'uuid', 'taskUuid',
            'workStatusUuid',
            'workStatus' => function ($model) {
                return $model->operationStatus;
            },
            'operationTemplateUuid',
            'operationTemplate' => function ($model) {
                return $model->operationTemplate;
            }, 'startDate', 'endDate',
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
            'taskUuid' => Yii::t('app', 'Этап'),
            'workStatusUuid' => Yii::t('app', 'Uuid статуса'),
            'workStatus' => Yii::t('app', 'Статус'),
            'operationTemplateUuid' => Yii::t('app', 'Uuid шаблона'),
            'operationTemplate' => Yii::t('app', 'Шаблон'),
            'startDate' => Yii::t('app', 'Начальная дата'),
            'endDate' => Yii::t('app', 'Конечная дата'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'изменен'),
        ];
    }

    /**
     * Объект связанного поля.
     *
     * @return \yii\db\ActiveRecord
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
     * @return \yii\db\ActiveQuery
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
     * @return \yii\db\ActiveQuery
     */
    public function getOperationTemplate()
    {
        return $this->hasOne(
            OperationTemplate::class, ['uuid' => 'operationTemplateUuid']
        );
    }
}
