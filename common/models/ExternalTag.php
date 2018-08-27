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
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "external_tag".
 *
 * @category Category
 * @package  Common\models
 * @link     http://www.toirus.ru
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $systemUuid
 * @property string $tag
 * @property string $value
 * @property string $equation
 * @property string $target
 * @property string $equipmentUuid
 * @property string $actionTypeUuid
 * @property string $taskEquipmentStageUuid
 * @property string $createdAt
 * @property string $changedAt
 */

class ExternalTag extends ActiveRecord
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
     * Table name.
     *
     * @return string
     */
    public static function tableName()
    {
        return 'external_tag';
    }

    /**
     * Свойства объекта со связанными данными.
     *
     * @return array
     */
    public function fields()
    {
        return ['_id', 'uuid', 'tag', 'value', 'equation', 'target',
            'system' => function ($model) {
                return $model->system;
            },
            'equipment' => function ($model) {
                return $model->equipment;
            },
            'actionType' => function ($model) {
                return $model->actionType;
            },
            'taskEquipmentStage' => function ($model) {
                return $model->taskEquipmentStage;
            },
            'createdAt', 'changedAt'
        ];
    }

    /**
     * Rules.
     *
     * @return array
     */
    public function rules()
    {
        return [
            [
                [
                    'uuid',
                    'systemUuid',
                    'tag',
                    'equation',
                    'target',
                    'equipmentUuid',
                    'taskEquipmentStageUuid',
                    'actionTypeUuid'
                ],
                'required'
            ],
            [['createdAt', 'changedAt'], 'safe'],
            [
                [
                    'uuid',
                    'systemUuid',
                    'equipmentUuid',
                    'value',
                    'equation',
                    'target',
                    'taskEquipmentStageUuid',
                    'actionTypeUuid'
                ],
                'string', 'max' => 50
            ],
            [['tag'], 'string', 'max' => 200],
        ];
    }

    /**
     * Метки для свойств.
     *
     * @return array
     */
    public function attributeLabels()
    {
        return [
            '_id' => Yii::t('app', '№'),
            'uuid' => Yii::t('app', 'Uuid'),
            'systemUuid' => Yii::t('app', 'Система'),
            'tag' => Yii::t('app', 'Название тега'),
            'value' => Yii::t('app', 'Текущее значение тега'),
            'equation' => Yii::t('app', 'Выражение'),
            'target' => Yii::t('app', 'Сравниваемое значение тега'),
            'equipmentUuid' => Yii::t('app', 'Оборудование'),
            'actionTypeUuid' => Yii::t('app', 'Тип события'),
            'taskEquipmentStageUuid' => Yii::t('app', 'Шаблон задачи оборудования'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
        ];
    }

    /**
     * Проверка целостности модели?
     *
     * @return bool
     */
    public function upload()
    {
        if ($this->validate()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Объект связанного поля.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getExternalSystem()
    {
        return $this->hasOne(
            ExternalSystem::className(), ['uuid' => 'systemUuid']
        );
    }

    /**
     * Объект связанного поля.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEquipment()
    {
        return $this->hasOne(Equipment::className(), ['uuid' => 'equipmentUuid']);
    }

    /**
     * Объект связанного поля.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getActionType()
    {
        return $this->hasOne(
            ActionType::className(), ['uuid' => 'actionTypeUuid']
        );
    }

    /**
     * Объект связанного поля.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTaskEquipmentStage()
    {
        return $this->hasOne(
            TaskEquipmentStage::className(), ['uuid' => 'taskEquipmentStageUuid']
        );
    }
}
