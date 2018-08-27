<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\db\ActiveRecord;
/**
 * This is the model class for table "taskverdict".
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $title
 * @property string $taskTypeUuid
 * @property string $createdAt
 * @property string $changedAt
 */
class TaskVerdict extends ActiveRecord
{
    const NOT_DEFINED = "0916D468-A631-4FC9-898C-04B7C9415284";
    const INSPECTED = "DFD29CF9-A817-41CD-B78C-3AC44C8A4747";

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
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
        return 'task_verdict';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uuid', 'title', 'taskTypeUuid'], 'required'],
            [['createdAt', 'changedAt'], 'safe'],
            [['uuid', 'taskTypeUuid'], 'string', 'max' => 45],
            [['title'], 'string', 'max' => 100],
        ];
    }

    public function fields()
    {
        return ['_id','uuid', 'title',
            'taskType' => function ($model) {
                return $model->taskType;
            },
            'createdAt', 'changedAt'
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
            'title' => Yii::t('app', 'Название'),
            'taskTypeUuid' => Yii::t('app', 'Тип задачи'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
        ];
    }

    public function getTaskType()
    {
        return $this->hasOne(TaskType::className(), ['uuid' => 'taskTypeUuid']);
    }
}
