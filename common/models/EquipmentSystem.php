<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "equipment_system".
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $title
 * @property string $titleUser
 * @property string $createdAt
 * @property string $changedAt
 */
class EquipmentSystem extends ActiveRecord
{
    const EQUIPMENT_SYSTEM_ELECTRO = "323F448A-22D5-4DB9-B3D6-48866A269DA8";
    const EQUIPMENT_SYSTEM_GAS = "9DF0207B-014A-4927-902C-AB123479925F";
    const EQUIPMENT_SYSTEM_SANTECH = "15AE4915-6092-4187-8C49-E6D54185D86C";
    const EQUIPMENT_SYSTEM_HEAT = "6FD9F381-0E15-4E0F-B0F1-5E3C92B9D6C1";
    const EQUIPMENT_SYSTEM_ROOF = "410B0CC6-2A20-4D24-B013-AA58F4BFC232";
    const EQUIPMENT_SYSTEM_WALL = "1DE9E879-6A8A-4EE3-BA57-496C36695775";
    const EQUIPMENT_SYSTEM_BUILD = "59BA74C5-9E7B-4DDE-A490-7E33CA652ED9";
    const EQUIPMENT_SYSTEM_MAIN = "0CE911BC-C2B8-4F38-992A-E1FB86BFE8D7";
    const EQUIPMENT_SYSTEM_VENT = "6CCA55FA-291E-4A3C-82EB-5127B189FB21";
    const EQUIPMENT_SYSTEM_LIFT = "51A23018-0E93-4F4C-97CA-3CB38A729D5C";

    const EQUIPMENT_SYSTEM_TECHNO = "ECB3CAED-24AF-441C-8763-F88340F794F1";

    /**
     * Behaviors.
     *
     * @return array
     */
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
     * Название таблицы.
     *
     * @return string
     *
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'equipment_system';
    }

    /**
     * Rules.
     *
     * @return array
     *
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uuid', 'titleUser', 'title'], 'required'],
            [['createdAt', 'changedAt'], 'safe'],
            [['uuid', 'titleUser'], 'string', 'max' => 50],
            [['title'], 'string', 'max' => 100],
        ];
    }

    /**
     * Labels.
     *
     * @return array
     *
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            '_id' => Yii::t('app', '№'),
            'uuid' => Yii::t('app', 'Uuid'),
            'title' => Yii::t('app', 'Название'),
            'titleUser' => Yii::t('app', 'Специализация'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
        ];
    }
}
