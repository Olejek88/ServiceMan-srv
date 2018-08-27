<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\db\ActiveRecord;
/**
 * This is the model class for table "attribute_type".
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $name
 * @property int $refresh
 * @property string $units
 * @property int $type
 * @property string $createdAt
 * @property string $changedAt
 */
class AttributeType extends ActiveRecord
{
    const ATTRIBUTE_TYPE_FILE = 1;
    const ATTRIBUTE_TYPE_VALUE = 2;
    const ATTRIBUTE_TYPE_STRING = 3;

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
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'attribute_type';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uuid'], 'required'],
            [['createdAt', 'changedAt', 'type'], 'safe'],
            [['uuid','units'], 'string', 'max' => 50],
            [['refresh','type'], 'integer'],
            [['name'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            '_id' => Yii::t('app', '№'),
            'uuid' => Yii::t('app', 'Uuid'),
            'name' => Yii::t('app', 'Название'),
            'refresh' => Yii::t('app', 'Обновляемый'),
            'units' => Yii::t('app', 'Единицы измерения'),
            'type' => Yii::t('app', 'Тип аттрибута'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
        ];
    }
}
