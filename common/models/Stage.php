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
 * This is the model class for table "stage".
 *
 * @category Category
 * @package  Common\models
 * @author   Максим Шумаков <ms.profile.d@gmail.com>
 * @license  http://www.yiiframework.com/license/ License name
 * @link     http://www.toirus.ru
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $comment
 * @property string $taskUuid
 * @property string $equipmentUuid
 * @property string $stageStatusUuid
 * @property string $stageVerdictUuid
 * @property string $stageTemplateUuid
 * @property string $startDate
 * @property string $endDate
 * @property integer $flowOrder
 * @property string $createdAt
 * @property string $changedAt
 */
class Stage extends ActiveRecord
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
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'createdAt',
                'updatedAtAttribute' => 'changedAt',
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * Table name
     *
     * @inheritdoc
     *
     * @return string
     */
    public static function tableName()
    {
        return 'stage';
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
                    'comment',
                    'taskUuid',
                    'equipmentUuid',
                    'stageStatusUuid',
                    'stageVerdictUuid',
                    'stageTemplateUuid',
                    'flowOrder'
                ],
                'required'
            ],
            [['comment'], 'string'],
            [['startDate', 'endDate', 'createdAt', 'changedAt'], 'safe'],
            [['flowOrder'], 'integer'],
            [
                [
                    'uuid',
                    'taskUuid',
                    'equipmentUuid',
                    'stageStatusUuid',
                    'stageVerdictUuid',
                    'stageTemplateUuid'
                ],
                'string',
                'max' => 45
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
        return ['_id','uuid', 'comment',
            'taskUuid',
            'equipmentUuid',
            'equipment' => function ($model) {
                return $model->equipment;
            },
            'stageStatusUuid',
            'stageStatus' => function ($model) {
                return $model->stageStatus;
            },
            'stageVerdictUuid',
            'stageVerdict' => function ($model) {
                return $model->stageVerdict;
            },
            'stageTemplateUuid',
            'stageTemplate' => function ($model) {
                return $model->stageTemplate;
            }, 'startDate', 'endDate','flowOrder', 'createdAt', 'changedAt',
            'operations' => function ($model) {
                return $model->operations;
            }
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
            'comment' => Yii::t('app', 'Комментарий'),
            'taskUuid' => Yii::t('app', 'Задача'),
            'equipmentUuid' => Yii::t('app', 'Оборудование'),
            'stageStatusUuid' => Yii::t('app', 'Статус'),
            'stageVerdictUuid' => Yii::t('app', 'Вердикт'),
            'stageTemplateUuid' => Yii::t('app', 'Шаблон'),
            'startDate' => Yii::t('app', 'Начальаня дата'),
            'endDate' => Yii::t('app', 'Коненая дата'),
            'flowOrder' => Yii::t('app', 'Сортировка'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
        ];
    }

    /**
     * Связанное поле
     *
     * @return Equipment|ActiveQuery
     */
    public function getEquipment()
    {
        return $this->hasOne(Equipment::className(), ['uuid' => 'equipmentUuid']);
    }

    /**
     * Связанное поле
     *
     * @return Task|ActiveQuery
     */
    public function getTask()
    {
        return $this->hasOne(Task::className(), ['uuid' => 'taskUuid']);
    }

    /**
     * Связанное поле
     *
     * @return StageStatus|ActiveQuery
     */
    public function getStageStatus()
    {
        return $this->hasOne(
            StageStatus::className(),
            ['uuid' => 'stageStatusUuid']
        );
    }

    /**
     * Связанное поле
     *
     * @return StageVerdict|ActiveQuery
     */
    public function getStageVerdict()
    {
        return $this->hasOne(
            StageVerdict::className(),
            ['uuid' => 'stageVerdictUuid']
        );
    }

    /**
     * Связанное поле
     *
     * @return StageTemplate|ActiveQuery
     */
    public function getStageTemplate()
    {
        return $this->hasOne(
            StageTemplate::className(),
            ['uuid' => 'stageTemplateUuid']
        );
    }

    /**
     * Связанное поле
     *
     * @return string
     */
    public function getStageFullName()
    {
        $stageTemplate = $this->hasOne(
            StageTemplate::className(),
            ['uuid' => 'stageTemplateUuid']
        )->one();
        return $stageTemplate['title'] . ' [' . $this->createdAt . ']';
    }


    /**
     * Связанное поле
     *
     * @return Operation[]|ActiveQuery
     */
    public function getOperations()
    {
        return $this->hasMany(Operation::className(), ['stageUuid' => 'uuid']);
    }

    /**
     * Видимо костыль для того чтоб не создавать рекурсию при получении объекта.
     *
     * @param object $objectParse Object
     *
     * @return object
     */
    public function parseObject($objectParse)
    {
        $array = [];
        if ($objectParse !== null) {
            foreach ($objectParse as $value => $property) {
                $array[$value] = $property;
            }
        }

        $result = (object) $array;
        return $result;
    }
}
