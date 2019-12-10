<?php

namespace common\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "organization_sub".
 *
 * @property int $_id
 * @property string $uuid
 * @property string $masterUuid
 * @property string $subUuid
 * @property string $createdAt
 * @property string $changedAt
 *
 * @property Organization $masterUu
 * @property Organization $subUu
 */
class OrganizationSub extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'organization_sub';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['uuid', 'masterUuid', 'subUuid'], 'required'],
            [['createdAt', 'changedAt'], 'safe'],
            [['uuid', 'masterUuid', 'subUuid'], 'string', 'max' => 45],
            [['uuid'], 'unique'],
            [['masterUuid', 'subUuid'], 'unique', 'targetAttribute' => ['masterUuid', 'subUuid']],
            [['masterUuid'], 'exist', 'skipOnError' => true, 'targetClass' => Organization::class, 'targetAttribute' => ['masterUuid' => 'uuid']],
            [['subUuid'], 'exist', 'skipOnError' => true, 'targetClass' => Organization::class, 'targetAttribute' => ['subUuid' => 'uuid']],
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
            'masterUuid' => 'Master Uuid',
            'subUuid' => 'Sub Uuid',
            'createdAt' => 'Created At',
            'changedAt' => 'Changed At',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getMasterUu()
    {
        return $this->hasOne(Organization::class, ['uuid' => 'masterUuid']);
    }

    /**
     * @return ActiveQuery
     */
    public function getSubUu()
    {
        return $this->hasOne(Organization::class, ['uuid' => 'subUuid']);
    }
}
