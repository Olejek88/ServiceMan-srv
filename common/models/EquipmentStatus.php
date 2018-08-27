<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\db\ActiveRecord;
/**
 * This is the model class for table "equipmentstatus".
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $title
 * @property string $createdAt
 * @property string $changedAt
 */
class EquipmentStatus extends ActiveRecord
{
    const NOT_MOUNTED = "62A9AA68-9FE5-4D8C-A4B8-34278B95E51E";
    const WORK = "61C5007F-AE18-4C4E-BD57-737A20EF9EBC";
    const NEED_CHECK = "D818A97E-B6EB-4AEC-9168-174C780E365B";
    const NEED_REPAIR = "7D0713CC-E79D-48D3-A2A2-60898A70BD8A";
    const NOT_WORK = "7B9C5D15-4079-489F-AF73-5135C36B330A";
    const UNKNOWN = "ED20012C-629A-4275-9BFA-A81D08B45758";

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
        return 'equipment_status';
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
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
        ];
    }
}
