<?php

namespace common\models;

use common\components\ZhkhActiveRecord;
use Yii;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "equipment_register".
 *
 * @property int $_id
 * @property string $uuid
 * @property string $oid идентификатор организации
 * @property string $equipmentUuid
 * @property string $registerTypeUuid
 * @property string $userUuid
 * @property string $date
 * @property string $description
 * @property int $createdAt
 * @property int $changedAt
 *
 * @property User $user
 * @property ActiveQuery $registerType
 * @property Equipment $equipment
 */
class EquipmentRegister extends ZhkhActiveRecord
{
    public const DESCRIPTION = 'Лог оборудования';

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
            [['uuid', 'userUuid', 'registerTypeUuid', 'equipmentUuid', 'date'], 'required'],
            [['data'], 'safe'],
            [['uuid', 'userUuid', 'registerTypeUuid', 'equipmentUuid'], 'string', 'max' => 50],
            [['description', 'oid'], 'string', 'max' => 250],
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
     * @return ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Users::class, ['uuid' => 'userUuid']);
    }

    /**
     * Объект связанного поля.
     *
     * @return ActiveQuery
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
     * @return ActiveQuery
     */
    public function getEquipment()
    {
        return $this->hasOne(
            Equipment::class, ['uuid' => 'equipmentUuid']
        );
    }

    public function fields()
    {
        $fields = parent::fields();
        return $fields;
//        return ['uuid',
//            'equipment' => function ($model) {
//                return $model->equipment;
//            },
//            'user' => function ($model) {
//                return $model->user;
//            },
//            'registerTypeUuid',
//            'registerType' => function ($model) {
//                return $model->registerType;
//            }, 'date', 'description', 'createdAt', 'changedAt'
//        ];
    }

    function getActionPermissions()
    {
        return array_merge_recursive(parent::getActionPermissions(), [
            'read' => [
                'list',
                'form',
            ],
            'edit' => [
                'new',
            ]]);
    }
}