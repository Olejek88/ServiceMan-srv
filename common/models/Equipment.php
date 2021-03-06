<?php

namespace common\models;

use common\components\ZhkhActiveRecord;
use Yii;
use yii\base\InvalidConfigException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\Exception;
use yii\db\Expression;

/**
 * This is the model class for table "equipment".
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $oid идентификатор организации
 * @property string $title
 * @property string $equipmentTypeUuid
 * @property string $serial
 * @property string $tag
 * @property string $equipmentStatusUuid
 * @property string $inputDate
 * @property string $testDate
 * @property integer $period
 * @property string $replaceDate
 * @property string $objectUuid
 * @property string $createdAt
 * @property string $changedAt
 * @property boolean $deleted
 *
 * @property EquipmentStatus $equipmentStatus
 * @property EquipmentType $equipmentType
 * @property Objects $object
 * @property string $nextDate
 * @property string $fullTitle
 * @property string $address
 * @property Photo $photo
 * @property User $user
 * @property TaskTemplateEquipment[] $taskTemplateEquipments
 */
class Equipment extends ZhkhActiveRecord
{
    public const DESCRIPTION = 'Оборудование';

    /**
     * Behaviors.
     *
     * @return array
     */
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
     * Table name.
     *
     * @return string
     */
    public static function tableName()
    {
        return 'equipment';
    }

    /**
     * Свойства объекта со связанными данными.
     *
     * @return array
     */
    public function fields()
    {
        $fields = parent::fields();
        return $fields;
//        return ['_id', 'uuid', 'title',
//            'objectUuid',
//            'object' => function ($model) {
//                return $model->object;
//            },
//            'equipmentTypeUuid',
//            'equipmentType' => function ($model) {
//                return $model->equipmentType;
//            },
//            'equipmentStatusUuid',
//            'equipmentStatus' => function ($model) {
//                return $model->equipmentStatus;
//            },
//            'serial', 'period', 'testDate', 'inputDate', 'replaceDate', 'tag', 'deleted',
//            'createdAt', 'changedAt'
//        ];
    }

    /**
     * Rules.
     *
     * @return array
     */
    public function rules()
    {
        return [
            [
                [
                    'uuid',
                    'title',
                    'equipmentTypeUuid',
                    'equipmentStatusUuid'
                ],
                'required'
            ],
            [['testDate', 'inputDate', 'nextDate', 'replaceDate', 'createdAt', 'changedAt'], 'safe'],
            [['deleted'], 'boolean'],
            [['period'], 'integer'],
            [
                [
                    'uuid',
                    'equipmentTypeUuid',
                    'equipmentStatusUuid',
                    'serial',
                    'tag',
                    'oid',
                    'objectUuid'
                ],
                'string', 'max' => 50
            ],
            [
                [
                    'title'
                ],
                'string', 'max' => 150
            ],
            [['oid'], 'exist', 'targetClass' => Organization::class, 'targetAttribute' => ['oid' => 'uuid']],
            [['oid'], 'checkOrganizationOwn'],

        ];
    }

    /**
     * Метки для свойств.
     *
     * @return array
     */
    public function attributeLabels()
    {
        return [
            '_id' => Yii::t('app', '№'),
            'uuid' => Yii::t('app', 'Uuid'),
            'title' => Yii::t('app', 'Название'),
            'equipmentTypeUuid' => Yii::t('app', 'Тип элемента'),
            'equipmentType' => Yii::t('app', 'Тип'),
            'testDate' => Yii::t('app', 'Дата последней поверки'),
            'period' => Yii::t('app', 'Период поверки (дн.)'),
            'inputDate' => Yii::t('app', 'Дата ввода в эксплуатацию'),
            'nextDate' => Yii::t('app', 'Дата следующей поверки'),
            'replaceDate' => Yii::t('app', 'Дата замены'),
            'equipmentStatusUuid' => Yii::t('app', 'Статус'),
            'equipmentStatus' => Yii::t('app', 'Статус'),
            'objectUuid' => Yii::t('app', 'Объект'),
            'object' => Yii::t('app', 'Объект'),
            'tag' => Yii::t('app', 'Метка'),
            'serial' => Yii::t('app', 'Заводской номер'),
            'address' => Yii::t('app', 'Адрес'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
        ];
    }

    /**
     * Объект связанного поля.
     *
     * @return ActiveQuery
     */
    public function getEquipmentStatus()
    {
        return $this->hasOne(
            EquipmentStatus::class, ['uuid' => 'equipmentStatusUuid']
        );
    }

    /**
     * Объект связанного поля.
     *
     * @return ActiveQuery
     */
    public function getEquipmentType()
    {
        return $this->hasOne(
            EquipmentType::class, ['uuid' => 'equipmentTypeUuid']
        );
    }

    /**
     * Объект связанного поля.
     *
     * @return ActiveQuery
     */
    public function getObject()
    {
        return $this->hasOne(Objects::class, ['uuid' => 'objectUuid']);
    }

    public function getPhoto()
    {
        return $this->hasMany(Photo::class, ['equipmentUuid' => 'uuid']);
    }

    public function getNextDate()
    {
        $seconds = strtotime($this->testDate) + ($this->period * 3600 * 24);
        return date('Y-m-d', $seconds);
    }

    /**
     * Объект связанного поля.
     *
     * @return string
     */
    public function getFullTitle()
    {
        return $this['object']->getFullTitle() . ' [' . $this['title'] . ']';
    }

    public static function getFullTitleStatic($equipment)
    {
        $objectFullTitle = Objects::getFullTitleStatic($equipment['object']);
        return $objectFullTitle . ' [' . $equipment['title'] . ']';
    }

    public function getAddress()
    {
        if ($this['object'] && $this['object']['house'])
            return $this['object']['house']['street']->title . ', ' . $this['object']['house']->number . ' - ' . $this['object']->title;
        else
            return '';
    }

    public static function getAddressStatic($model)
    {
        if ($model['object'] && $model['object']['house'])
            return $model['object']['house']['street']['title'] . ', ' . $model['object']['house']['number'] . ' - ' . $model['object']['title'];
        else
            return '';
    }

    /**
     * @return string
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function getUser()
    {
        $userSystems = UserSystem::find()
            ->where(['equipmentSystemUuid' => $this->equipmentType['equipmentSystem']['uuid']])
            ->all();
        foreach ($userSystems as $userSystem) {
            if ($userSystem['user']['active'])
                return $userSystem['user'];
        }
        return null;
    }

    public function getTaskTemplateEquipments()
    {
        return $this->hasMany(TaskTemplateEquipment::class, ['equipmentUuid' => 'uuid']);
    }

    function getActionPermissions()
    {
        return array_merge_recursive(parent::getActionPermissions(), [
            'read' => [
                'measure',
                'index-check',
                'tree',
                'table',
                'tree-user',
                'tree-street',
                'operations',
                'measures',
                'status',
                'serial',
                'select-task',
                'timeline-all',
                'timeline',
                'move',
                'remove',
                'edit',
                'edit-table',
            ],
            'edit' => [
                'deleted',
                'rename',
                'save',
                'add',
                'new',
            ]]);
    }
}
