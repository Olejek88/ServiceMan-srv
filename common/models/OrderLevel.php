<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\db\ActiveRecord;
/**
 * This is the model class for table "orderlevel".
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $title
 * @property string $icon
 * @property string $createdAt
 * @property string $changedAt
 */
class OrderLevel extends ActiveRecord
{
    const LEVEL_1 = 'DB392A36-A970-4BB3-96AB-FEF0F5FEBB95';
    const LEVEL_2 = 'CEE7D7C4-3050-40DD-8E2D-073D2A18FDB9';
    const LEVEL_3 = '673CE002-26EC-4132-944D-9F29A596FCFD';
    const LEVEL_4 = '7C18B8BE-D744-4692-84FC-CA3EF904EF0A';
    const LEVEL_5 = 'EE9D663F-72B5-434A-BEB7-F9B1637920FB';

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
        return 'order_level';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uuid', 'title'], 'required'],
            [['createdAt', 'changedAt'], 'safe'],
            [['uuid'], 'string', 'max' => 50],
            [['icon'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg'],
            [['title'], 'string', 'max' => 100],
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
            'title' => Yii::t('app', 'Название'),
            'icon' => Yii::t('app', 'Иконка'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
        ];
    }

    public function upload()
    {
        if ($this->validate()) {
            return true;
        } else {
            return false;
        }
    }
}
