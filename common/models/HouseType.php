<?php

namespace common\models;

use common\components\ZhkhActiveRecord;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "house_type".
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $oid идентификатор организации
 * @property string $gis_id глобальный идентификатор в ГИС ЖКХ
 * @property string $title
 * @property string $createdAt
 * @property string $changedAt
 */
class HouseType extends ZhkhActiveRecord
{
    public const HOUSE_TYPE_PRIVATE = "6A0AB43B-AEA7-44BE-8E6C-001F4F854A4F";
    public const HOUSE_TYPE_MKD = "583C8D20-CE23-401D-8E90-0FFCDAA6BE50";
    public const HOUSE_TYPE_COMMERCE = "A156A75E-CE7A-4ED9-87D5-0FCEF89DBC9F";
    public const HOUSE_TYPE_BUDGET = "9AA5A1B4-224C-4BF4-B79D-2039CF314C40";
    public const HOUSE_TYPE_TOWNHOUSE = "EB9E96C0-0F93-447F-9F25-F325F3B67546";

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
        return '{{%house_type}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uuid', 'title'], 'required'],
            [['createdAt', 'changedAt'], 'safe'],
            [['uuid', 'title', 'oid'], 'string', 'max' => 50],
            [['oid'], 'exist', 'targetClass' => Organization::class, 'targetAttribute' => ['oid' => 'uuid']],
            [['oid'], 'checkOrganizationOwn'],
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
