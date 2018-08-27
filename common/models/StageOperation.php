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
 * This is the model class for table "stage_operation".
 *
 * @category Category
 * @package  Common\models
 * @author   Максим Шумаков <ms.profile.d@gmail.com>
 * @license  http://www.yiiframework.com/license/ License name
 * @link     http://www.toirus.ru
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $stageTemplateUuid
 * @property string $operationTemplateUuid
 * @property string $createdAt
 * @property string $changedAt
 * @property OperationTemplate $operationTemplate
 * @property StageTemplate $stageTemplate
 */
class StageOperation extends ActiveRecord
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
        return 'stage_operation';
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
            [['uuid', 'stageTemplateUuid', 'operationTemplateUuid'], 'required'],
            [['createdAt', 'changedAt'], 'safe'],
            [['uuid'], 'string', 'max' => 50],
            [['stageTemplateUuid', 'operationTemplateUuid'], 'string', 'max' => 45],
            [
                ['operationTemplateUuid'],
                'exist',
                'skipOnError' => true,
                'targetClass' => OperationTemplate::className(),
                'targetAttribute' => ['operationTemplateUuid' => 'uuid']
            ],
            [
                ['stageTemplateUuid'],
                'exist',
                'skipOnError' => true,
                'targetClass' => StageTemplate::className(),
                'targetAttribute' => ['stageTemplateUuid' => 'uuid']
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
            'stageTemplateUuid' => Yii::t('app', 'Uuid шаблона этапа'),
            'stageTemplate' => Yii::t('app', 'Шаблон этапа'),
            'operationTemplateUuid' => Yii::t('app', 'Uuid шаблона операции'),
            'operationTemplate' => Yii::t('app', 'Шаблон операции'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
        ];
    }

    /**
     * Link
     *
     * @return ActiveQuery
     */
    public function getOperationTemplate()
    {
        return $this->hasOne(
            OperationTemplate::className(), ['uuid' => 'operationTemplateUuid']
        );
    }

    /**
     * Link
     *
     * @return ActiveQuery
     */
    public function getStageTemplate()
    {
        return $this->hasOne(
            StageTemplate::className(), ['uuid' => 'stageTemplateUuid']
        );
    }
}
