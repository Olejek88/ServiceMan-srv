<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "equipment_type_tree".
 *
 * @property integer $_id
 * @property integer $parent
 * @property integer $child
 * @property string $createdAt
 * @property string $changedAt
 *
 * @property EquipmentType $child0
 * @property EquipmentType $parent0
 */
class EquipmentTypeTree extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'equipment_type_tree';
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
            [['child'], 'exist', 'skipOnError' => true, 'targetClass' => EquipmentType::className(), 'targetAttribute' => ['child' => '_id']],
            [['parent'], 'exist', 'skipOnError' => true, 'targetClass' => EquipmentType::className(), 'targetAttribute' => ['parent' => '_id']],
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
        return $this->hasOne(EquipmentType::className(), ['_id' => 'child']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParent0()
    {
        return $this->hasOne(EquipmentType::className(), ['_id' => 'parent']);
    }
}
