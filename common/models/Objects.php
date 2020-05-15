<?php

namespace common\models;

use common\components\ZhkhActiveRecord;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\Expression;

/**
 * This is the model class for table "object".
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $oid идентификатор организации
 * @property string $gis_id глобальный идентификатор в ГИС ЖКХ
 * @property string $title
 * @property double $square
 * @property string $objectStatusUuid
 * @property string $houseUuid
 * @property string $createdAt
 * @property string $changedAt
 * @property string $objectTypeUuid
 * @property boolean $deleted
 *
 * @property House $house
 * @property ObjectStatus $objectStatus
 * @property Photo $photo
 * @property string $fullTitle
 * @property ObjectContragent[] $contragents
 * @property ObjectType $objectType
 */
class Objects extends ZhkhActiveRecord
{
    public const DESCRIPTION = 'Объекты';

    public const COMMON_OBJECT_TITLE = 'Общий';

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
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'object';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uuid', 'objectStatusUuid', 'objectTypeUuid', 'houseUuid'], 'required'],
            [['square', 'house','createdAt', 'changedAt'], 'safe'],
            [['deleted'], 'boolean'],
            [['uuid', 'title', 'objectStatusUuid', 'objectTypeUuid', 'houseUuid', 'oid'], 'string', 'max' => 50],
            [['oid'], 'exist', 'targetClass' => Organization::class, 'targetAttribute' => ['oid' => 'uuid']],
            [['oid'], 'checkOrganizationOwn'],
        ];
    }

    public function fields()
    {
        $fields = parent::fields();
        return $fields;
//        return [
//            '_id',
//            'uuid',
//            'title',
//            'square',
//            'objectStatusUuid',
//            'objectStatus' => function ($model) {
//                return $model->objectStatus;
//            },
//            'objectTypeUuid',
//            'objectType' => function($model) {
//                return $model->objectType;
//            },
//            'houseUuid',
//            'house' => function ($model) {
//                return $model->house;
//            },
//            'createdAt',
//            'changedAt',
//        ];
    }

    public function getObjectStatus()
    {
        return $this->hasOne(ObjectStatus::class, ['uuid' => 'objectStatusUuid']);
    }

    public function getHouse()
    {
        return $this->hasOne(House::class, ['uuid' => 'houseUuid']);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            '_id' => Yii::t('app', '№'),
            'uuid' => Yii::t('app', 'Uuid'),
            'title' => Yii::t('app', 'Название'),
            'square' => Yii::t('app', 'Площадь'),
            'objectStatusUuid' => Yii::t('app', 'Статус объекта'),
            'objectStatus' => Yii::t('app', 'Статус объекта'),
            'objectTypeUuid' => Yii::t('app', 'Тип объекта'),
            'objectType' => Yii::t('app', 'Тип объекта'),
            'houseUuid' => Yii::t('app', 'Дом'),
            'house' => Yii::t('app', 'Дом'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getPhoto()
    {
        return $this->hasMany(Photo::class, ['objectUuid' => 'uuid']);
    }

    /**
     * @return ActiveQuery
     */
    public function getObjectType()
    {
        return $this->hasOne(ObjectType::class, ['uuid' => 'objectTypeUuid']);
    }

    public function getFullTitle()
    {
        $house = $this->house;
        return 'ул.' . $house->street->title . ', д.' . $house->number . ' - ' . $this->title;
    }

    public static function getFullTitleStatic($object)
    {
        $house = $object['house'];
        return 'ул.' . $house['street']['title'] . ', д.' . $house['number'] . ' - ' . $object['title'];
    }

    function getActionPermissions()
    {
        return array_merge_recursive(parent::getActionPermissions(), [
            'read' => [
                'table',
                'tree',
                'new',
                'house',
            ],
            'edit' => [
                'save',
                'edit',
                'remove',
                'remove-link',
            ]]);
    }

    /**
     * @return ActiveQuery
     */
    public function getContragents()
    {
        return $this->hasMany(ObjectContragent::class, ['objectUuid' => 'uuid']);
    }
}
