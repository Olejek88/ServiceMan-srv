<?php

namespace common\models;

use common\components\ZhkhActiveRecord;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "users_ext_system".
 *
 * @property int $_id
 * @property string $uuid
 * @property string $oid
 * @property string $usersUuid
 * @property string $extId
 * @property string $createdAt
 * @property string $changedAt
 *
 * @property Users $users
 * @property Organization $organization
 */
class UsersExtSystem extends ZhkhActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'users_ext_system';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['uuid', 'oid', 'usersUuid', 'extId'], 'required'],
            [['createdAt', 'changedAt'], 'safe'],
            [['uuid', 'oid', 'usersUuid', 'extId'], 'string', 'max' => 45],
            [['uuid'], 'unique'],
            [['oid', 'usersUuid', 'extId'], 'unique', 'targetAttribute' => ['oid', 'usersUuid', 'extId']],
            [['usersUuid'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['usersUuid' => 'uuid']],
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
            'usersUuid' => 'Users Uuid',
            'extId' => 'Ext ID',
            'createdAt' => 'Created At',
            'changedAt' => 'Changed At',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasOne(Users::class, ['uuid' => 'usersUuid']);
    }

    /**
     * @return ActiveQuery
     */
    public function getOrganization()
    {
        return $this->hasOne(Organization::class, ['uuid' => 'oid']);
    }
}
