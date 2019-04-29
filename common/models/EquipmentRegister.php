<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "equipment_register".
 *
 * @property string $uuid
 * @property string $oid идентификатор организации
 * @property string $equipmentUuid
 * @property string $registerTypeUuid
 * @property string $userUuid
 * @property string $date
 * @property string $description
 *
 * @property User $user
 * @property Equipment $equipment
 * @property EquipmentRegisterType $equipmentRegisterType
 */

class EquipmentRegister extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'equipment_register';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uuid','userUuid', 'registerTypeUuid', 'equipmentUuid', 'date'], 'required'],
            [['data'], 'safe'],
            [['uuid','userUuid', 'registerTypeUuid', 'equipmentUuid'], 'string', 'max' => 50],
            [['description','oid'], 'string', 'max' => 250],
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
            'uuid' => Yii::t('app', 'Uuid'),
            'equipmentUuid' => Yii::t('app', 'Оборудование'),
            'equipment' => Yii::t('app', 'Оборудование'),
            'userUuid' => Yii::t('app', 'Пользователь'),
            'user' => Yii::t('app', 'Пользователь'),
            'description' => Yii::t('app', 'Запись'),
            'date' => Yii::t('app', 'Дата'),
            'registerType' => Yii::t('app', 'Тип события'),
            'registerTypeUuid' => Yii::t('app', 'Тип события'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Users::class, ['uuid' => 'userUuid']);
    }

    /**
     * Объект связанного поля.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRegisterType()
    {
        return $this->hasOne(
            EquipmentRegisterType::class, ['uuid' => 'registerTypeUuid']
        );
    }

    /**
     * Объект связанного поля.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEquipment()
    {
        return $this->hasOne(
            Equipment::class, ['uuid' => 'equipmentUuid']
        );
    }

    public function fields()
    {
        return ['uuid',
            'equipment' => function ($model) {
                return $model->equipment;
            },
            'user' => function ($model) {
                return $model->user;
            },
            'registerTypeUuid',
            'registerType' => function ($model) {
                return $model->registerType;
            }, 'date', 'description', 'createdAt', 'changedAt'
        ];
    }
}