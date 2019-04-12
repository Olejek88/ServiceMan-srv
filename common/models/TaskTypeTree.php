<?php
namespace common\models;

use yii\db\ActiveRecord;


/**
 * This is the model class for table "task_type_tree".
 *
 * @property integer $_id
 * @property integer $parent
 * @property integer $child
 * @property string $createdAt
 * @property string $changedAt
 *
 * @property TaskType $child0
 * @property TaskType $parent0
 */
class TaskTypeTree extends ActiveRecord
{
    /**
     * Table name.
     *
     * @return string
     */
    public static function tableName()
    {
        return 'task_type_tree';
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
                'targetClass' => TaskType::class,
                'targetAttribute' => ['child' => '_id']
            ],
            [
                ['parent'],
                'exist',
                'skipOnError' => true,
                'targetClass' => TaskType::class,
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
        return $this->hasOne(TaskType::class, ['_id' => 'child']);
    }

    /**
     * Объект связанного поля.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getParent0()
    {
        return $this->hasOne(TaskType::class, ['_id' => 'parent']);
    }
}
