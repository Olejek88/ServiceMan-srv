<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "task_cron".
 *
 * @property integer $_id
 * @property string $taskUuid
 * @property string $last_execution_date
 * @property string $next_execution_date
 * @property integer $status
 */
class TaskCron extends ActiveRecord
{
    const STATUS_WAITING = 9;
    const STATUS_RUNNING = 10;
    const STATUS_FINISHED = 11;

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
        return 'task_cron';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['taskUuid', 'last_execution_date', 'next_execution_date', 'status'], 'required'],
            [['createdAt', 'changedAt'], 'safe'],
            [['taskUuid', 'last_execution_date', 'next_execution_date'], 'string', 'max' => 45],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            '_id' => Yii::t('app', '№'),
            'taskUuid' => Yii::t('app', 'Задача'),
            'last_execution_date' => Yii::t('app', 'Дата последнего запуска'),
            'next_execution_date' => Yii::t('app', 'Дата следующего запуска'),
            'status' => Yii::t('app', 'Статус выполнения'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
        ];
    }
}
