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

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "stage_type_tree".
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
 * @property StageType $child0
 * @property StageType $parent0
 */
class StageTypeTree extends ActiveRecord
{
    /**
     * Table name.
     *
     * @return string
     */
    public static function tableName()
    {
        return 'stage_type_tree';
    }

    /**
     * Rules.
     *
     * @return array
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
                'targetClass' => StageType::className(),
                'targetAttribute' => ['child' => '_id']
            ],
            [
                ['parent'],
                'exist',
                'skipOnError' => true,
                'targetClass' => StageType::className(),
                'targetAttribute' => ['parent' => '_id']
            ],
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
            '_id' => 'Id',
            'parent' => 'Parent',
            'child' => 'Child',
            'createdAt' => 'Created At',
            'changedAt' => 'Changed At',
        ];
    }

    /**
     * Объект связанного поля.
     *
     * @return ActiveQuery
     */
    public function getChild0()
    {
        return $this->hasOne(StageType::className(), ['_id' => 'child']);
    }

    /**
     * Объект связанного поля.
     *
     * @return ActiveQuery
     */
    public function getParent0()
    {
        return $this->hasOne(StageType::className(), ['_id' => 'parent']);
    }
}
