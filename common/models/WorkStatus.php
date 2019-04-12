<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\db\ActiveRecord;
/**
 * This is the model class for table "work_status".
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $title
 * @property string $createdAt
 * @property string $changedAt
 */
class WorkStatus extends ActiveRecord
{
    const NEW_OPERATION = "18D3D5D4-336F-4B25-BA2B-00A6C7D5EB6C";
    const IN_WORK = "78063CCA-4463-45AD-9124-88CEA2B51017";
    const COMPLETE = "626FC9E9-9F1F-4DE7-937D-74DAD54ED751";
    const UN_COMPLETE = "0F733A22-B65A-4D96-AF86-34F7E6A62B0B";
    const CANCELED = "1A277EB1-1A22-400F-9E03-F094E19FEEDE";

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
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
        return 'work_status';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uuid', 'title'], 'required'],
            [['createdAt', 'changedAt'], 'safe'],
            [['uuid'], 'string', 'max' => 45],
            [['title'], 'string', 'max' => 200],
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

    public function getWorkStatus()
    {
        return $this->hasOne(WorkStatus::class, ['uuid' => 'workStatusUuid']);
    }
}
