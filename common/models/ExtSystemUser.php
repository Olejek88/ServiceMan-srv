<?php

namespace common\models;

use common\components\ZhkhActiveRecord;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "ext_system_user".
 *
 * @property int $_id
 * @property string $uuid
 * @property string $oid
 * @property string $extId
 * @property string $fullName
 * @property string $rawData
 * @property string $integrationClass
 * @property string $createdAt
 * @property string $changedAt
 *
 * @property Organization $organization
 */
class ExtSystemUser extends ZhkhActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ext_system_user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['uuid', 'oid', 'extId', 'fullName'], 'required'],
            [['createdAt', 'changedAt'], 'safe'],
            [['uuid', 'oid', 'extId'], 'string', 'max' => 45],
            [['fullName',], 'string', 'max' => 64],
            [['integrationClass',], 'string', 'max' => 64],
            [['uuid'], 'unique'],
            [['oid', 'extId', 'integrationClass'], 'unique', 'targetAttribute' => ['oid', 'extId', 'integrationClass']],
            [['oid'], 'exist', 'targetClass' => Organization::class, 'targetAttribute' => ['oid' => 'uuid']],
            [['oid'], 'checkOrganizationOwn'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            '_id' => 'Id',
            'uuid' => 'Uuid',
            'oid' => 'Oid',
            'extId' => 'Ext ID',
            'fullName' => 'Full name',
            'rawData' => 'Raw data',
            'integrationClass' => 'Integration class',
            'createdAt' => 'Created At',
            'changedAt' => 'Changed At',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getOrganization()
    {
        return $this->hasOne(Organization::class, ['uuid' => 'oid']);
    }
}
