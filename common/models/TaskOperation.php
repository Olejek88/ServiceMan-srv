<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "task_operation".
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $taskTemplateUuid
 * @property string $operationTemplateUuid
 * @property string $createdAt
 * @property string $changedAt
 * @property OperationTemplate $operationTemplate
 * @property TaskTemplate $taskTemplate
 */
class TaskOperation extends ActiveRecord
{
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
     * Название таблицы.
     *
     * @return string
     *
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'task_operation';
    }

    /**
     * Rules.
     *
     * @return array
     *
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uuid', 'taskTemplateUuid', 'operationTemplateUuid'], 'required'],
            [['createdAt', 'changedAt'], 'safe'],
            [['uuid'], 'string', 'max' => 50],
            [['taskTemplateUuid', 'operationTemplateUuid'], 'string', 'max' => 45],
            [
                ['operationTemplateUuid'],
                'exist',
                'skipOnError' => true,
                'targetClass' => OperationTemplate::class,
                'targetAttribute' => ['operationTemplateUuid' => 'uuid']
            ],
            [
                ['taskTemplateUuid'],
                'exist',
                'skipOnError' => true,
                'targetClass' => TaskTemplate::class,
                'targetAttribute' => ['taskTemplateUuid' => 'uuid']
            ],
        ];
    }

    /**
     * Labels.
     *
     * @return array
     *
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            '_id' => Yii::t('app', '№'),
            'uuid' => Yii::t('app', 'Uuid'),
            'taskTemplateUuid' => Yii::t('app', 'Шаблона этапа'),
            'taskTemplate' => Yii::t('app', 'Шаблон этапа'),
            'operationTemplateUuid' => Yii::t('app', 'Шаблон операции'),
            'operationTemplate' => Yii::t('app', 'Шаблон операции'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
        ];
    }

    /**
     * Link
     *
     * @return ActiveQuery
     */
    public function getOperationTemplate()
    {
        return $this->hasOne(
            OperationTemplate::class, ['uuid' => 'operationTemplateUuid']
        );
    }

    /**
     * Link
     *
     * @return ActiveQuery
     */
    public function getTaskTemplate()
    {
        return $this->hasOne(
            TaskTemplate::class, ['uuid' => 'taskTemplateUuid']
        );
    }
}
