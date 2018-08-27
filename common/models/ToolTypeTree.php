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
 * This is the model class for table "tool_type_tree".
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
 * @property ToolType $child0
 * @property ToolType $parent0
 */
class ToolTypeTree extends ActiveRecord
{
    /**
     * Table name.
     *
     * @return string
     */
    public static function tableName()
    {
        return 'tool_type_tree';
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
                'targetClass' => ToolType::className(),
                'targetAttribute' => ['child' => '_id']
            ],
            [
                ['parent'],
                'exist', 'skipOnError' => true,
                'targetClass' => ToolType::className(),
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
     * @return \yii\db\ActiveQuery
     */
    public function getChild0()
    {
        return $this->hasOne(ToolType::className(), ['_id' => 'child']);
    }

    /**
     * Объект связанного поля.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getParent0()
    {
        return $this->hasOne(ToolType::className(), ['_id' => 'parent']);
    }
}
