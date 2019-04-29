<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "task_template".
 *
 * @property integer $_id
 * @property string $oid идентификатор организации
 * @property string $uuid
 * @property string $title
 * @property string $description
 * @property integer $normative
 * @property string $taskTypeUuid
 * @property string $createdAt
 * @property string $changedAt
 *
 * @property TaskType $taskType
 */
class TaskTemplate extends ActiveRecord
{
    const DEFAULT_TASK = "138C39D3-F0F0-443C-95E7-698A5CAC6E74";

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
     * Имя таблицы.
     *
     * @inheritdoc
     *
     * @return string
     */
    public static function tableName()
    {
        return 'task_template';
    }

    /**
     * Rules
     *
     * @inheritdoc
     *
     * @return mixed
     */
    public function rules()
    {
        return [
            [
                [
                    'uuid',
                    'title',
                    'description',
                    'normative',
                    'taskTypeUuid'
                ],
                'required',
            ],
            [['description'], 'string'],
            [['normative'], 'filter', 'filter' => 'intval'],
            [['createdAt', 'changedAt'], 'safe'],
            [['uuid', 'taskTypeUuid', 'oid'], 'string', 'max' => 45],
            [['title'], 'string', 'max' => 100],
        ];
    }

    /**
     * Fields
     *
     * @return array
     */
    public function fields()
    {
        return [
            '_id', 'uuid', 'title', 'description', 'taskTypeUuid',
            'normative',
            'taskType' => function ($model) {
                return $model->taskType;
            }, 'createdAt', 'changedAt'
        ];
    }

    /**
     * Attribute labels
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
            'title' => Yii::t('app', 'Название'),
            'description' => Yii::t('app', 'Описание'),
            'normative' => Yii::t('app', 'Норматив'),
            'taskTypeUuid' => Yii::t('app', 'Тип задачи'),
            'taskType' => Yii::t('app', 'Тип задачи'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
        ];
    }

    /**
     * Link
     *
     * @return \yii\db\ActiveQuery | \common\models\TaskType
     */
    public function getTaskType()
    {
        return $this->hasOne(TaskType::class, ['uuid' => 'taskTypeUuid']);
    }

}
