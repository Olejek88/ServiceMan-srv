<?php
namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "user_system".
 *
 * @property integer $_id
 * @property string $oid идентификатор организации
 * @property string $uuid
 * @property string $userUuid
 * @property string $equipmentSystemUuid
 * @property string $createdAt
 * @property string $changedAt
 */
class UserSystem extends ActiveRecord
{
    /**
     * Название таблицы.
     * @return string
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_system';
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
            [['uuid', 'userUuid', 'equipmentSystemUuid'], 'required'],
            [['createdAt', 'changedAt'], 'safe'],
            [['uuid', 'userUuid', 'equipmentSystemUuid'], 'string', 'max' => 50],
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
            'userUuid' => Yii::t('app', 'Пользователь'),
            'system' => Yii::t('app', 'Система'),
            'equipmentSystemUuid' => Yii::t('app', 'Система'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
        ];
    }

    /**
     * Объект связанного поля.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Users::class, ['uuid' => 'userUuid']);
    }

    /**
     * Объект связанного поля.
     * @return \yii\db\ActiveQuery
     */
    public function getSystem()
    {
        return $this->hasOne(EquipmentSystem::class, ['uuid' => 'equipmentSystemUuid']);
    }
}
