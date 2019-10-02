<?php

namespace common\models;

use common\components\ZhkhActiveRecord;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;
use yii\db\Exception;

/**
 * This is the model class for table "user_house".
 *
 * @property integer $_id
 * @property string $oid идентификатор организации
 * @property string $uuid
 * @property string $userUuid
 * @property string $houseUuid
 * @property string $createdAt
 * @property string $changedAt
 *
 * @property ActiveQuery $user
 * @property ActiveQuery $house
 */
class UserHouse extends ZhkhActiveRecord
{
    public const DESCRIPTION = 'Связь пользователей с домами';

    /**
     * Название таблицы.
     * @return string
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_house';
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
            [['uuid', 'userUuid', 'houseUuid'], 'required'],
            [['createdAt', 'changedAt'], 'safe'],
            [['uuid', 'userUuid', 'houseUuid', 'oid'], 'string', 'max' => 50],
            [['oid'], 'exist', 'targetClass' => Organization::class, 'targetAttribute' => ['oid' => 'uuid']],
            [['oid'], 'checkOrganizationOwn'],
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
            'user' => Yii::t('app', 'Пользователь'),
            'house' => Yii::t('app', 'Дом'),
            'userUuid' => Yii::t('app', 'Пользователь'),
            'houseUuid' => Yii::t('app', 'Дом'),
            'fileName' => Yii::t('app', 'Имя файла'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
        ];
    }

    /**
     * Объект связанного поля.
     *
     * @return ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Users::class, ['uuid' => 'userUuid']);
    }

    /**
     * Объект связанного поля.
     * @return ActiveQuery
     */
    public function getHouse()
    {
        return $this->hasOne(House::class, ['uuid' => 'houseUuid']);
    }

    /**
     * @param $houseUuid
     * @return UserHouse|null
     * @throws InvalidConfigException
     * @throws Exception
     */
    public static function getUserName($houseUuid)
    {
        $model = UserHouse::find()->where(["houseUuid" => $houseUuid])->one();
        if (!empty($model)) {
            return $model['user']->name;
        }
        return null;
    }

    function getActionPermissions()
    {
        return array_merge(parent::getActionPermissions(), [
            'read' => [
                'tree',
                'change',
            ],
            'edit' => [
                'create-default',
                'name',
            ]]);
    }
}
