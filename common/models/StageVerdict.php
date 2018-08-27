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
 * This is the model class for table "stage_verdict".
 *
 * @category Category
 * @package  Common\models
 * @author   Максим Шумаков <ms.profile.d@gmail.com>
 * @license  http://www.yiiframework.com/license/ License name
 * @link     http://www.toirus.ru
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $title
 * @property string $icon
 * @property string $stageTypeUuid
 * @property string $createdAt
 * @property string $changedAt
 */
class StageVerdict extends ActiveRecord
{
    const NO_INSPECTED = "B85EF192-8CD2-4471-AACA-5863C4A8F377";
    const NOT_DEFINED = "AF3AF1C4-5CAF-4797-8217-E5998608857E";

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
        return 'stage_verdict';
    }

    /**
     * Fields
     *
     * @return array
     */
    public function fields()
    {
        return ['_id','uuid', 'title', 'icon',
            'stageType' => function ($model) {
                return $model->stageType;
            }, 'createdAt', 'changedAt'
        ];
    }

    /**
     * Rules
     *
     * @inheritdoc
     *
     * @return mixed
     */
    public function rules()
    {
        return [
            [['uuid', 'title', 'stageTypeUuid'], 'required'],
            [['createdAt', 'changedAt'], 'safe'],
            [['uuid', 'stageTypeUuid'], 'string', 'max' => 45],
            [['title'], 'string', 'max' => 100],
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
            'title' => Yii::t('app', 'Название'),
            'icon' => Yii::t('app', 'Иконка'),
            'stageTypeUuid' => Yii::t('app', 'Uuid типа шаблона этапа'),
            'stageType' => Yii::t('app', 'Тип шаблона этапа'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
        ];
    }

    /**
     * Link
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStageType()
    {
        return $this->hasOne(
            StageType::className(), ['uuid' => 'stageTypeUuid']
        );
    }
}
