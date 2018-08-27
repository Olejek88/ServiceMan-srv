<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "operation_type_tree".
 *
 * @property integer $_id
 * @property integer $parent
 * @property integer $child
 * @property string $createdAt
 * @property string $changedAt
 *
 * @property OperationType $child0
 * @property OperationType $parent0
 */
class OperationTypeTree extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'operation_type_tree';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['parent', 'child'], 'required'],
            [['parent', 'child'], 'integer'],
            [['createdAt', 'changedAt'], 'safe'],
            [['child'], 'exist', 'skipOnError' => true, 'targetClass' => OperationType::className(), 'targetAttribute' => ['child' => '_id']],
            [['parent'], 'exist', 'skipOnError' => true, 'targetClass' => OperationType::className(), 'targetAttribute' => ['parent' => '_id']],
        ];
    }

    /**
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
     * @return \yii\db\ActiveQuery
     */
    public function getChild0()
    {
        return $this->hasOne(OperationType::className(), ['_id' => 'child']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParent0()
    {
        return $this->hasOne(OperationType::className(), ['_id' => 'parent']);
    }
}
