<?php

namespace common\models;

use common\components\ZhkhActiveRecord;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\Expression;

/**
 * This is the model class for table "task_template_equipment_type".
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $taskTemplateUuid
 * @property string $equipmentTypeUuid
 * @property string $createdAt
 * @property string $changedAt
 * @property string $oid
 *
 * @property EquipmentType $equipmentType
 * @property Organization $organization
 * @property TaskTemplate $taskTemplate
 */
class TaskTemplateEquipmentType extends ZhkhActiveRecord
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
        return 'task_template_equipment_type';
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
                    'equipmentTypeUuid',
                ],
                'required'
            ],
            [['createdAt', 'changedAt'], 'safe'],
            [['uuid'], 'string', 'max' => 50],
            [
                [
                    'taskTemplateUuid',
                    'equipmentTypeUuid'
                ],
                'string',
                'max' => 45
            ],
            [
                ['equipmentTypeUuid'],
                'exist',
                'skipOnError' => true,
                'targetClass' => EquipmentType::class,
                'targetAttribute' => ['equipmentTypeUuid' => 'uuid']
            ],
            [
                ['taskTemplateUuid'],
                'exist',
                'skipOnError' => true,
                'targetClass' => TaskTemplate::class,
                'targetAttribute' => ['taskTemplateUuid' => 'uuid']
            ],
            [['oid'], 'exist', 'targetClass' => Organization::class, 'targetAttribute' => ['oid' => 'uuid']],
            [['oid'], 'checkOrganizationOwn'],
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
            'taskTemplateUuid' => Yii::t('app', 'Шаблон задачи'),
            'taskTemplate' => Yii::t('app', 'Шаблон задачи'),
            'equipmentTypeUuid' => Yii::t('app', 'Тип оборудования'),
            'equipmentType' => Yii::t('app', 'Тип оборудования'),
            'oid' => Yii::t('app', 'Организация'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
        ];
    }

    /**
     * Link
     *
     * @return ActiveQuery
     */
    public function getEquipmentType()
    {
        return $this->hasOne(
            EquipmentType::class, ['uuid' => 'equipmentTypeUuid']
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

    /**
     * @return ActiveQuery
     */
    public function getOrganization()
    {
        return $this->hasOne(Organization::class, ['uuid' => 'oid']);
    }

    function getActionPermissions()
    {
        return array_merge_recursive(parent::getActionPermissions(), [
            'edit' => [
                'form',
                'new',
                'delete-task',
            ]]);
    }
}
