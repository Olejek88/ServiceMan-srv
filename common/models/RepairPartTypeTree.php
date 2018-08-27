<?php
/**
 * PHP Version 7.0
 *
 * @category Category
 * @package  Common\models
 * @author   Дмитрий Логачев <demonwork@yandex.ru>
 * @license  http://www.yiiframework.com/license/ License name
 * @link     http://www.toirus.ru
 */

namespace common\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "repair_part_type_tree".
 *
 * @category Category
 * @package  Common\models
 * @author   Дмитрий Логачев <demonwork@yandex.ru>
 * @license  http://www.yiiframework.com/license/ License name
 * @link     http://www.toirus.ru
 *
 * @property integer $_id
 * @property integer $parent
 * @property integer $child
 * @property string $createdAt
 * @property string $changedAt
 *
 * @property RepairPartType $child0
 * @property RepairPartType $parent0
 */
class RepairPartTypeTree extends ActiveRecord
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
        return 'repair_part_type_tree';
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
            [['parent', 'child'], 'required'],
            [['parent', 'child'], 'integer'],
            [['createdAt', 'changedAt'], 'safe'],
            [
                ['child'],
                'exist',
                'skipOnError' => true,
                'targetClass' => RepairPartType::className(),
                'targetAttribute' => ['child' => '_id']
            ],
            [
                ['parent'],
                'exist',
                'skipOnError' => true,
                'targetClass' => RepairPartType::className(),
                'targetAttribute' => ['parent' => '_id']
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
            '_id' => 'Id',
            'parent' => 'Parent',
            'child' => 'Child',
            'createdAt' => 'Created At',
            'changedAt' => 'Changed At',
        ];
    }

    /**
     * Link
     *
     * @return \yii\db\ActiveQuery | \common\models\RepairPartType
     */
    public function getChild0()
    {
        return $this->hasOne(RepairPartType::className(), ['_id' => 'child']);
    }

    /**
     * Link
     *
     * @return \yii\db\ActiveQuery | \common\models\RepairPartType
     */
    public function getParent0()
    {
        return $this->hasOne(RepairPartType::className(), ['_id' => 'parent']);
    }
}
