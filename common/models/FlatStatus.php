<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\db\ActiveRecord;
/**
 * This is the model class for table "flat_status".
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $title
 * @property string $createdAt
 * @property string $changedAt
 */
class FlatStatus extends ActiveRecord
{
    const FLAT_STATUS_OK = '32562AA9-DE1D-436D-A0ED-5F5789DB8712';
    const FLAT_STATUS_NO_ENTRANCE = 'FEA3CC91-DD48-4264-AEF6-F91947A1B8EB';
    const FLAT_STATUS_ABSENT = 'BB6E24F2-6FA5-4E9A-83C8-5E1F4D51789B';

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
        return 'flat_status';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uuid', 'title'], 'required'],
            [['createdAt', 'changedAt'], 'safe'],
            [['uuid', 'title'], 'string', 'max' => 50],
        ];
    }

    public function fields()
    {
        return [
            '_id',
            'uuid',
            'title',
            'createdAt',
            'changedAt',
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
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
        ];
    }
}
