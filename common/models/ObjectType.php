<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "object_type".
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $title
 * @property string $createdAt
 * @property string $changedAt
 */
class ObjectType extends ActiveRecord
{
    const OBJECT_TYPE_GENERAL = "42686CFC-34D0-45FF-95A4-04B0D865EC35";
    const OBJECT_TYPE_COMMERCE = "587B526B-A5C2-4B30-92DD-C63F796333A6";
    const OBJECT_TYPE_INPUT = "F68A562B-8F61-476F-A3E7-5666F9CEAFA1";

    const OBJECT_TYPE_FLAT = "80237148-9DBB-4315-A99D-D83CA5258C69";            //  Квартира
    const OBJECT_TYPE_SYSTEM_HVS = "CB9E9A67-FFE5-4168-8407-F2CAFBF76069";      //  ХВС
    const OBJECT_TYPE_SYSTEM_GVS = "4923FFF8-B010-4043-90E6-C9665BDFBAD7";      //  ГВС
    const OBJECT_TYPE_SYSTEM_HEAT = "5C1711D5-5597-41FB-A32E-59C2AFB5E00B";     //  тепло
    const OBJECT_TYPE_SYSTEM_ROOF = "6A973F1E-1A6D-4C64-B55C-3EE4FB149C5E";     //  крыша
    const OBJECT_TYPE_SYSTEM_WALL = "A2DA436F-7230-48B2-8991-3913DA5DFB39";     //  фасад
    const OBJECT_TYPE_SYSTEM_YARD = "FFDAC354-66CF-41CB-9820-E8328B426D32";     //  двор
    const OBJECT_TYPE_SYSTEM_ENTRANCE = "73EF98B1-8B3F-4F29-96E1-772A4959AC1F"; //  подъезд
    const OBJECT_TYPE_SYSTEM_SEWER = "0D6ABB06-C170-4E03-B7C7-58DD8A3B7FCD";    //  канализация
    const OBJECT_TYPE_SYSTEM_ELECTRO = "49650A0E-3C02-43F8-9D92-830EA329B93B";  //  электричество
    const OBJECT_TYPE_SYSTEM_SMOKE = "5EFD55F0-4E33-49FB-85A6-CF7873662E01";    //  вентиляция
    const OBJECT_TYPE_SYSTEM_GAS = "FDBAF46A-1764-4F25-A2B0-AC66C5102E2F";      //  газ
    const OBJECT_TYPE_SYSTEM_BASEMENT = "3D163AC7-3061-4796-B535-0B39C08E9377"; //  подвал

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
        return '{{%object_type}}';
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
