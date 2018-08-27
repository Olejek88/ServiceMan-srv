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
 * This is the model class for table "operation_tool".
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
 * @property string $toolUuid
 * @property string $quantity
 * @property string $createdAt
 * @property string $changedAt
 */
class OperationTool extends ActiveRecord
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
        return 'operation_tool';
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
            [['uuid', 'operationTemplateUuid', 'toolUuid', 'quantity'], 'required'],
            [['createdAt', 'changedAt'], 'safe'],
            [['quantity'], 'integer'],
            [['uuid', 'operationTemplateUuid', 'toolUuid'], 'string', 'max' => 45],
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
            'toolUuid' => Yii::t('app', 'Uuid инструмента'),
            'tool' => Yii::t('app', 'Инструмент'),
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
            'toolUuid',
            'tool' => function ($model) {
                return $model->tool;
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
     * @return ActiveQuery | Tool
     */
    public function getTool()
    {
        return $this->hasOne(Tool::className(), ['uuid' => 'toolUuid']);
    }

}
