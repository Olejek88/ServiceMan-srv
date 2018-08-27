<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "equipment_register".
 *
 * @property string $uuid
 * @property string $equipmentUuid
 * @property string $registerType
 * @property string $userUuid
 * @property string $date
 * @property string $fromParameterUuid
 * @property string $toParameterUuid
 * @property User $userId0
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
            [['uuid','userUuid', 'registerTypeUuid', 'equipmentUuid', 'fromParameterUuid', 'toParameterUuid'], 'string', 'max' => 50],
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
            'userUuid' => Yii::t('app', 'Пользователь'),
            'fromParameterUuid' => Yii::t('app', 'С параметра'),
            'toParameterUuid' => Yii::t('app', 'В параметр'),
            'date' => Yii::t('app', 'Дата'),
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
        return $this->hasOne(Users::className(), ['uuid' => 'userUuid']);
    }

    /**
     * Объект связанного поля.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRegisterType()
    {
        return $this->hasOne(
            EquipmentRegisterType::className(), ['uuid' => 'registerTypeUuid']
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
            Equipment::className(), ['uuid' => 'equipmentUuid']
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
            'registerType' => function ($model) {
                return $model->registerType;
            }, 'date', 'fromParameterUuid', 'toParameterUuid', 'createdAt', 'changedAt'
        ];
    }
}