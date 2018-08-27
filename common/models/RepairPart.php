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
use yii\db\ActiveRecord;

/**
 * This is the model class for table "repair_part".
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
 * @property string $repairPartTypeUuid
 * @property string $createdAt
 * @property string $changedAt
 */
class RepairPart extends ActiveRecord
{
    /**
     * Название таблицы.
     *
     * @return string
     *
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'repair_part';
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
            [['uuid', 'title', 'repairPartTypeUuid'], 'required'],
            [['createdAt', 'changedAt'], 'safe'],
            [['uuid'], 'string', 'max' => 50],
            [['title'], 'string', 'max' => 100],
            [['repairPartTypeUuid'], 'string', 'max' => 45],
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
            'title' => Yii::t('app', 'Название'),
            'repairPartTypeUuid' => Yii::t('app', 'Uuid типа запчасти'),
            'repairPartType' => Yii::t('app', 'Тип запчасти'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
        ];
    }

    /**
     * Fields.
     *
     * @return array
     */
    public function fields()
    {
        return [
            '_id',
            'uuid',
            'title',
            'repairPartTypeUuid',
            'repairPartType' => function ($model) {
                return $model->repairPartType;
            },
            'createdAt',
            'changedAt',
        ];
    }

    /**
     * Link
     *
     * @return \yii\db\ActiveQuery | \common\models\RepairPartType
     */
    public function getRepairPartType()
    {
        return $this->hasOne(
            RepairPartType::className(), ['uuid' => 'repairPartTypeUuid']
        );
    }

}
