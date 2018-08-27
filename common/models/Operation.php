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
 * This is the model class for table "operation".
 *
 * @category Category
 * @package  Common\models
 * @author   Максим Шумаков <ms.profile.d@gmail.com>
 * @license  http://www.yiiframework.com/license/ License name
 * @link     http://www.toirus.ru
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $stageUuid
 * @property string $operationVerdictUuid
 * @property string $operationStatusUuid
 * @property string $operationTemplateUuid
 * @property string $startDate
 * @property string $endDate
 * @property integer $flowOrder
 * @property string $createdAt
 * @property string $changedAt
 * @property string $comment
 */
class Operation extends ActiveRecord
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
     * Название таблицы
     *
     * @inheritdoc
     *
     * @return string
     */
    public static function tableName()
    {
        return 'operation';
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
                    'stageUuid',
                    'operationVerdictUuid',
                    'operationStatusUuid',
                    'operationTemplateUuid',
                    'flowOrder'
                ],
                'required'
            ],
            [['startDate', 'endDate', 'createdAt', 'changedAt'], 'safe'],
            [['flowOrder'], 'integer'],
            [
                [
                    'uuid',
                    'stageUuid',
                    'operationVerdictUuid',
                    'operationStatusUuid',
                    'operationTemplateUuid'
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
        return ['_id', 'uuid', 'stageUuid',
            'operationStatusUuid',
            'operationStatus' => function ($model) {
                return $model->operationStatus;
            },
            'operationVerdictUuid',
            'operationVerdict' => function ($model) {
                return $model->operationVerdict;
            },
            'operationTemplateUuid',
            'operationTemplate' => function ($model) {
                return $model->operationTemplate;
            }, 'startDate', 'endDate', 'flowOrder',
            'createdAt', 'changedAt'
        ];
    }

    /**
     * Названия отрибутов
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
            'stageUuid' => Yii::t('app', 'Этап'),
            'operationVerdictUuid' => Yii::t('app', 'Uuid вердикта'),
            'operationVerdict' => Yii::t('app', 'Вердикт'),
            'operationStatusUuid' => Yii::t('app', 'Uuid статуса'),
            'operationStatus' => Yii::t('app', 'Статус'),
            'operationTemplateUuid' => Yii::t('app', 'Uuid шаблона'),
            'operationTemplate' => Yii::t('app', 'Шаблон'),
            'startDate' => Yii::t('app', 'Начальная дата'),
            'endDate' => Yii::t('app', 'Конечная дата'),
            'flowOrder' => Yii::t('app', 'Поле сортировки'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'изменен'),
            'comment' =>  Yii::t('app', 'Коментарий'),
        ];
    }

    /**
     * Объект связанного поля.
     *
     * @return \yii\db\ActiveRecord
     */
    public function getTaskStage()
    {
        $stage = Stage::find()
            ->select('*')
            ->where(['uuid' => $this->stageUuid])
            ->one();
        return $stage;
    }

    /**
     * Объект связанного поля.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOperationVerdict()
    {
        return $this->hasOne(
            OperationVerdict::className(), ['uuid' => 'operationVerdictUuid']
        );
    }

    /**
     * Объект связанного поля.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOperationStatus()
    {
        return $this->hasOne(
            OperationStatus::className(), ['uuid' => 'operationStatusUuid']
        );
    }

    /**
     * Объект связанного поля.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOperationTemplate()
    {
        return $this->hasOne(
            OperationTemplate::className(), ['uuid' => 'operationTemplateUuid']
        );
    }

    /**
     * Объект связанного поля.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEquipment()
    {
        return $this->hasOne(
            Equipment::className(), ['uuid' => 'operationTemplateUuid']
        );
    }
}
