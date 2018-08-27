<?php
/**
 * PHP Version 7.0
 *
 * @category Category
 * @package  Common\models
 * @author   Максим Шумаков <ms.profile.d@gmail.com>
 * @license  http://www.yiiframework.com/license/ License name
 * @link     http://www.toirus.ru
 */

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "task_equipment_stage".
 *
 * @category Category
 * @package  Common\models
 * @author   Дмитрий Логачев <demonwork@yandex.ru>
 * @license  http://www.yiiframework.com/license/ License name
 * @link     http://www.toirus.ru
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $taskTemplateUuid
 * @property string $equipmentStageUuid
 * @property string $period
 * @property string $createdAt
 * @property string $changedAt
 * @property EquipmentStage $equipmentStage
 * @property TaskTemplate $taskTemplate
 */
class TaskEquipmentStage extends ActiveRecord
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
                'class' => TimestampBehavior::className(),
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
        return 'task_equipment_stage';
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
            [
                [
                    'uuid',
                    'taskTemplateUuid',
                    'equipmentStageUuid',
                ],
                'required'
            ],
            [['createdAt', 'changedAt'], 'safe'],
            [['uuid'], 'string', 'max' => 50],
            [
                [
                    'taskTemplateUuid',
                    'equipmentStageUuid',
                    'period'
                ],
                'string',
                'max' => 45
            ],
            [
                ['equipmentStageUuid'],
                'exist',
                'skipOnError' => true,
                'targetClass' => EquipmentStage::className(),
                'targetAttribute' => ['equipmentStageUuid' => 'uuid']
            ],
            [
                ['taskTemplateUuid'],
                'exist',
                'skipOnError' => true,
                'targetClass' => TaskTemplate::className(),
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
            'taskTemplateUuid' => Yii::t('app', 'Uuid шаблона задачи'),
            'taskTemplate' => Yii::t('app', 'Шаблон задачи'),
            'equipmentStageUuid' => Yii::t(
                'app',
                'Uuid связи оборудования с этапом'
            ),
            'equipmentStage' => Yii::t('app', 'Связь оборудования с этапом'),
            'period' => Yii::t('app', 'Периодичность'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
        ];
    }

    /**
     * Link
     *
     * @return ActiveQuery
     */
    public function getEquipmentStage()
    {
        return $this->hasOne(
            EquipmentStage::className(), ['uuid' => 'equipmentStageUuid']
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
            TaskTemplate::className(), ['uuid' => 'taskTemplateUuid']
        );
    }
}
