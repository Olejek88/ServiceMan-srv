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
 * This is the model class for table "equipment_stage".
 *
 * @category Category
 * @package  Common\models
 * @author   Максим Шумаков <ms.profile.d@gmail.com>
 * @license  http://www.yiiframework.com/license/ License name
 * @link     http://www.toirus.ru
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $equipmentUuid
 * @property string $stageOperationUuid
 * @property string $createdAt
 * @property string $changedAt
 * @property Equipment $equipment
 * @property StageOperation $stageOperation
 */
class EquipmentStage extends ActiveRecord
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
        return 'equipment_stage';
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
            [['uuid', 'equipmentUuid', 'stageOperationUuid'], 'required'],
            [['createdAt', 'changedAt'], 'safe'],
            [['uuid'], 'string', 'max' => 50],
            [['equipmentUuid', 'stageOperationUuid'], 'string', 'max' => 45],
            [
                ['equipmentUuid'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Equipment::className(),
                'targetAttribute' => ['equipmentUuid' => 'uuid']
            ],
            [
                ['stageOperationUuid'],
                'exist',
                'skipOnError' => true,
                'targetClass' => StageOperation::className(),
                'targetAttribute' => ['stageOperationUuid' => 'uuid']
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
            'equipmentUuid' => Yii::t('app', 'Uuid оборудования'),
            'equipment' => Yii::t('app', 'Оборудование'),
            'stageOperationUuid' => Yii::t('app', 'Uuid связи этапа с операцией'),
            'stageOperation' => Yii::t('app', 'Связь этапа с операцией'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
        ];
    }

    /**
     * Link
     *
     * @return ActiveQuery
     */
    public function getEquipment()
    {
        return $this->hasOne(Equipment::className(), ['uuid' => 'equipmentUuid']);
    }

    /**
     * Link
     *
     * @return ActiveQuery
     */
    public function getStageOperation()
    {
        return $this->hasOne(
            StageOperation::className(), ['uuid' => 'stageOperationUuid']
        );
    }

}
