<?php

namespace common\models;

use common\components\ZhkhActiveRecord;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "comments".
 *
 * @property int $_id
 * @property string $uuid
 * @property string $oid
 * @property string $entityUuid
 * @property string $text
 * @property string $extId
 * @property string $extParentId
 * @property string $extParentType
 * @property string $rawData
 * @property string $date
 * @property string $integrationClass
 * @property string $createdAt
 * @property string $changedAt
 *
 * @property Organization $organization
 */
class Comments extends ZhkhActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'comments';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['uuid', 'oid', 'entityUuid'], 'required'],
            [['text', 'rawData'], 'string'],
            [['date', 'createdAt', 'changedAt'], 'safe'],
            [['uuid', 'oid', 'entityUuid', 'extId', 'extParentId', 'extParentType'], 'string', 'max' => 45],
            [['integrationClass'], 'string', 'max' => 128],
            [['uuid'], 'unique'],
            [['oid', 'extId', 'extParentId', 'extParentType', 'integrationClass'], 'unique', 'targetAttribute' => ['oid', 'extId', 'extParentId', 'extParentType', 'integrationClass']],
            [['oid'], 'exist', 'skipOnError' => true, 'targetClass' => Organization::class, 'targetAttribute' => ['oid' => 'uuid']],
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
            'entityUuid' => 'Entity Uuid',
            'text' => 'Text',
            'extId' => 'Ext ID',
            'extParentId' => 'Ext Parent ID',
            'extParentType' => 'Ext Parent Type',
            'rawData' => 'Raw Data',
            'date' => 'Date',
            'integrationClass' => 'Integration Class',
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
