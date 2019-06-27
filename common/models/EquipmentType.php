<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "equipment_type".
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $title
 * @property string $equipmentSystemUuid
 * @property string $createdAt
 * @property string $changedAt
 *
 * @property EquipmentSystem $equipmentSystem
 */
class EquipmentType extends ActiveRecord
{
    const EQUIPMENT_HVS = '7AB0B720-9FDB-448C-86C1-4649A7FCF279';
    const EQUIPMENT_GVS = '4F50C767-A044-465B-A69F-02DD321BC5FB';
    const EQUIPMENT_ELECTRICITY = 'B6904443-363B-4F01-B940-F47B463E66D8';
    const EQUIPMENT_HEAT_COUNTER = '42686CFC-34D0-45FF-95A4-04B0D865EC35';

    const EQUIPMENT_TYPE_BALCONY = '24AAD5EA-E4D9-4C8A-8ABB-F8261C24006E';
    const EQUIPMENT_HVS_COUNTER = '3AB137FC-1944-4DD4-9116-81BC8BFD4818';
    const EQUIPMENT_HVS_MAIN = '89D725FC-1A88-44BC-AB12-FBD4D2F8937C';
    const EQUIPMENT_HVS_PUMP = '7C6BD837-1262-4737-9F88-5F14761ED729';
    const EQUIPMENT_HVS_TOWER = '326C0544-F552-4381-B022-254FBC94EA65';

    const EQUIPMENT_GVS_MAIN = 'B38595F4-EAF1-43BD-84F5-EE2C1B7F52FA';
    const EQUIPMENT_GVS_PUMP = '39FEC794-2785-4C1E-84AA-C14AEBCE0C8C';
    const EQUIPMENT_GVS_TOWER = '04F24BFD-8DB5-4461-A364-A591FA990BD1';

    const EQUIPMENT_HEAT_MAIN = '1C648F81-CE80-46D0-A80E-003F8E1D0EFC';
    const EQUIPMENT_HEAT_TOWER = '5299B161-EDFE-4514-8983-585A87226C1C';
    const EQUIPMENT_HEAT_RADIATOR = '20E4E8C8-9779-4760-8918-42C1C71C071A';
    const EQUIPMENT_HEAT_PUMP = 'B52C7DE2-88BA-46E4-848E-8F71CAE3DAF1';

    const EQUIPMENT_ROOF = '6646DEA9-4D1B-4367-8927-F2A0F39C3957';
    const EQUIPMENT_ROOF_ENTRANCE = '2C458E63-26D9-486B-B013-A9D4D6BB46A0';
    const EQUIPMENT_ROOF_ROOM = 'FC1B4F06-4140-4987-B068-E4889EED9E86';
    const EQUIPMENT_ROOF_WATER_PIPE = 'E9E12F54-78DF-4912-A5BE-AE4F1A9DA10D';

    const EQUIPMENT_WALL = 'CC7E730F-2D79-4723-8DB8-7210345C416B';
    const EQUIPMENT_WALL_WATER = '328D5FC1-1EDC-4F7D-BBD8-7CEC2052FEC6';

    const EQUIPMENT_YARD = '776429BD-5D91-40A3-94CD-3280A707EA45';
    const EQUIPMENT_YARD_TBO = '04A9079A-1108-4DAD-B94E-F261CE1C2EF2';
    const EQUIPMENT_YARD_DRENAGE = 'A66B3143-9A74-4B24-B299-A4365746B36A';

    const EQUIPMENT_ENTRANCE_WINDOWS = '4986E2B7-B16F-4838-B628-D27948E9D671';
    const EQUIPMENT_ENTRANCE_DOOR = '3927D323-707C-4AED-B76F-109ABDADA1C6';
    const EQUIPMENT_ENTRANCE_TRASH_PIPE = '26FAFF05-6A7A-4EA4-BBA9-CC4A6B5C6B3C';
    const EQUIPMENT_ENTRANCE_STAIRS = '65E63695-E2EF-4D00-9721-B4E41A88B090';

    const EQUIPMENT_LIFT = 'C7987543-EA90-4EB0-894F-547E6F0A753A';
    const EQUIPMENT_ENTRANCE_DOOR_TAMBUR = '5DEC5E96-5C08-4CB1-8033-5157D0DAC062';
    const EQUIPMENT_ENTRANCE_MAIN = '9CADDCE4-DEA1-4CAC-8824-B3610B6F0E66';

    const EQUIPMENT_SEWER_PIPE = 'E3231B90-93D9-4054-8A2D-367A2CC751AA';
    const EQUIPMENT_SEWER_MAIN = '64CF1076-32B0-4E5F-B401-06330C07C111';
    const EQUIPMENT_SEWER_WELL = 'BABE06AD-FE44-4475-BF7B-22A141C70160';

    const EQUIPMENT_ELECTRICITY_COUNTER = '2511ACA5-EFBA-4956-846D-5B24F1DDF394';
    const EQUIPMENT_ELECTRICITY_VRU = 'F907BF72-101D-4396-AC8E-701F1821F6C5';
    const EQUIPMENT_ELECTRICITY_LEVEL_SHIELD = '4BA33570-CEBD-41E8-8778-B29AAC29B78B';
    const EQUIPMENT_ELECTRICITY_LIGHT = '843D578C-62D3-4DAC-81D0-19CA9EBCAB3C';
    const EQUIPMENT_ELECTRICITY_ENTRANCE_LIGHT = '87DB7F49-A98F-4CD3-B670-F005D89920AE';
    const EQUIPMENT_ELECTRICITY_ENTRANCE_PIPE = 'D235FA7F-774F-4FB7-A12E-EA6D08D61D1D';
    const EQUIPMENT_ELECTRICITY_HERD = '053D2E52-BD3D-4549-9207-E96D4C053E89';

    const EQUIPMENT_INTERNET = '7E6BB6A1-C91C-494C-9011-9D0A4472798E';
    const EQUIPMENT_CONDITIONER = 'D8C3809A-3A5C-4835-BBBE-8D8C196737D7';
    const EQUIPMENT_DOMOPHONE = '002F8B45-6783-470E-A759-D0272803794C';
    const EQUIPMENT_TV = '0D7EAF5E-315F-4348-A339-C00254E773A7';

    const EQUIPMENT_GAS = '07C0BBB4-3594-405B-8829-A8F3330AC27F';
    const EQUIPMENT_BASEMENT = 'FDBE2840-12D3-4182-AFC1-072A1CEFB44A';
    const EQUIPMENT_BASEMENT_ROOM = 'C7FDE255-3C2B-49B7-8D7D-C7F93057D4D9';
    const EQUIPMENT_BASEMENT_WINDOWS = '41CD23B4-D250-4FBD-9ED7-7FF788A3BA20';

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
        return 'equipment_type';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uuid', 'title'], 'required'],
            [['createdAt', 'changedAt'], 'safe'],
            [['uuid', 'title', 'equipmentSystemUuid'], 'string', 'max' => 45],
        ];
    }

    public function fields()
    {
        return [
            '_id',
            'uuid',
            'title',
            'equipmentSystemUuid',
            'equipmentSystem' => function ($model) {
                return $model->equipmentSystem;
            },
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
            'equipmentSystem' => Yii::t('app', 'Ин.Система'),
            'equipmentSystemUuid' => Yii::t('app', 'Ин.Система'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
        ];
    }

    /**
     * Объект связанного поля.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEquipmentSystem()
    {
        return $this->hasOne(
            EquipmentSystem::class, ['uuid' => 'equipmentSystemUuid']
        );
    }

}
