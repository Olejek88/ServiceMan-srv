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
 * This is the model class for table "operation_repair_part".
 *
 * @category Category
 * @package  Common\models
 * @author   Максим Шумаков <ms.profile.d@gmail.com>
 * @license  http://www.yiiframework.com/license/ License name
 * @link     http://www.toirus.ru
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $operationTemplateUuid
 * @property string $repairPartUuid
 * @property integer $quantity
 * @property string $createdAt
 * @property string $changedAt
 */
class OperationRepairPart extends ActiveRecord
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
        return 'operation_repair_part';
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
            [['uuid', 'operationTemplateUuid', 'repairPartUuid'], 'required'],
            [['quantity'], 'integer'],
            [['createdAt', 'changedAt'], 'safe'],
            [['uuid'], 'string', 'max' => 50],
            [['operationTemplateUuid', 'repairPartUuid'], 'string', 'max' => 45],
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
            'operationTemplateUuid' => Yii::t('app', 'Uuid шаблона операции'),
            'operationTemplate' => Yii::t('app', 'Шаблон операции'),
            'repairPartUuid' => Yii::t('app', 'Uuid запчасти'),
            'repairPart' => Yii::t('app', 'Запчасть'),
            'quantity' => Yii::t('app', 'Количество'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
        ];
    }

    /**
     * Fields.
     *
     * @return array
     *
     * @inheritdoc
     */
    public function fields()
    {
        return [
            '_id','uuid',
            'repairPartUuid',
            'repairPart' => function ($model) {
                return $model->repairPart;
            },
            'operationTemplateUuid',
            'quantity',
            'createdAt',
            'changedAt',
        ];
    }

    /**
     * Link
     *
     * @return ActiveQuery | Tool
     */
    public function getOperationTemplate()
    {
        return $this->hasOne(
            OperationTemplate::className(),
            ['uuid' => 'operationTemplateUuid']
        );
    }

    /**
     * Link
     *
     * @return ActiveQuery
     */
    public function getRepairPart()
    {
        return $this->hasOne(RepairPart::className(), ['uuid' => 'repairPartUuid']);
    }

}
