<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "task_type".
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $title
 * @property string $createdAt
 * @property string $changedAt
 */
class TaskType extends ActiveRecord
{
    const TASK_TYPE_CURRENT_REPAIR = 'E39AE9CC-E98B-485C-930D-62315076DBBE';
    const TASK_TYPE_PLAN_REPAIR = 'E624A870-2064-40CF-80A1-ADD414EBA4FB';
    const TASK_TYPE_CURRENT_CHECK = '13FC82D2-AE8C-42AF-A406-33E5036C33E1';
    const TASK_TYPE_NOT_PLANNED_CHECK = 'B66FB299-C099-496F-95D2-66BAEB73A8D2';
    const TASK_TYPE_SEASON_CHECK = '407A4C28-B1A4-485A-8F5C-4FA9C9CBE336';
    const TASK_TYPE_CONTROL = '444CBA6D-2082-4DD7-9CC2-6EB5EBF1C5E0';
    const TASK_TYPE_PLAN_TO = '3EE2B734-B957-4191-82A7-60119C2C8556';
    const TASK_TYPE_TO = '3EE2B734-B957-4191-82A7-60119C2C8556';
    const TASK_TYPE_NOT_PLAN_TO = '053D2E52-BD3D-4549-9207-E96D4C053E89';

    const TASK_TYPE_REPAIR = 'F99AE9CC-E98B-485C-930D-62315076DBBE';
    const TASK_TYPE_MEASURE = '47202738-6A35-447D-87CC-9274FED4CCAA';
    const TASK_TYPE_POVERKA = '87DB7F49-A98F-4CD3-B670-F005D89920AE';
    const TASK_TYPE_INSTALL = '831D61CC-147F-4AE3-AE55-C99901C6AA4C';

    const TASK_TYPE_VIEW = '7E2DB5D5-13CB-4BEB-A3F7-4B3A090922BB';
    const TASK_TYPE_REPLACE = '831D61CC-147F-4AE3-AE55-C99901C6AA4C';
    const TASK_TYPE_UNINSTALL = '0A7AB474-357B-4BFD-923D-7E7BE17D1B74';

    const TASK_TYPE_OVERHAUL = '6345EA99-CD30-41B5-98E9-43D4E8E3FF96';

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
        return 'task_type';
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
            [['uuid', 'title'], 'required'],
            [['createdAt', 'changedAt'], 'safe'],
            [['uuid'], 'string', 'max' => 45],
            [['title'], 'string', 'max' => 100],
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
            'title' => Yii::t('app', 'Название'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
        ];
    }
}
