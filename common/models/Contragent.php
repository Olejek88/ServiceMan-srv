<?php

namespace common\models;

use common\components\ZhkhActiveRecord;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * Contragent model
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $oid идентификатор организации
 * @property string $gis_id глобальный идентификатор в ГИС ЖКХ
 * @property string $title
 * @property string $address
 * @property string $phone
 * @property string $inn
 * @property string account
 * @property string $director
 * @property string $email
 * @property string $contragentTypeUuid
 * @property integer $deleted
 * @property string $createdAt
 * @property mixed $contragentType
 * @property string $changedAt
 *
 */
class Contragent extends ZhkhActiveRecord
{
    /**
     * Table name.
     *
     * @inheritdoc
     *
     * @return string
     */
    public static function tableName()
    {
        return '{{%contragent}}';
    }

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
     * Rules.
     *
     * @inheritdoc
     *
     * @return array
     */
    public function rules()
    {
        return [
            ['deleted', 'default', 'value' => Status::STATUS_DEFAULT],
            ['deleted', 'in', 'range' => [Status::STATUS_DEFAULT, Status::STATUS_ARCHIVED]],
            [['uuid', 'title', 'contragentTypeUuid', 'deleted'], 'required'],
            [['createdAt', 'changedAt'], 'safe'],
            [['uuid', 'title', 'oid', 'phone', 'inn', 'director', 'email', 'contragentTypeUuid'], 'string', 'max' => 50],
            [['address','account'], 'string', 'max' => 250],
            [['oid'], 'exist', 'targetClass' => Organization::class, 'targetAttribute' => ['oid' => 'uuid']],
            [['oid'], 'checkOrganizationOwn'],
        ];
    }

    public function fields()
    {
        return [
            '_id',
            'oid',
            'uuid',
            'title',
            'address',
            'phone',
            'inn',
            'account',
            'director',
            'email',
            'contragentTypeUuid',
            'contragentType' => function ($model) {
                return $model->contragentType;
            },
            'deleted',
            'createdAt',
            'changedAt',
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
            'title' => Yii::t('app', 'Наименование/ФИО'),
            'address' => Yii::t('app', 'Адрес'),
            'account' => Yii::t('app', 'Номер счета'),
            'phone' => Yii::t('app', 'Телефон'),
            'inn' => Yii::t('app', 'ИНН'),
            'director' => Yii::t('app', 'Комментарий'),
            'email' => Yii::t('app', 'Е-мэйл'),
            'contragentTypeUuid' => Yii::t('app', 'Тип'),
            'contragentType' => Yii::t('app', 'Тип'),
            'deleted' => Yii::t('app', 'Удален'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
        ];
    }

    public function getContragentType()
    {
        return $this->hasOne(ContragentType::class, ['uuid' => 'contragentTypeUuid']);
    }

    /**
     * Поиск контрагента по id и статусу.
     *
     * @param integer $id Ид пользователя.
     *
     * @inheritdoc
     *
     * @return Contragent
     */
    public static function findIdentity($id)
    {
        return static::findOne(['_id' => $id, 'deleted' => Status::STATUS_DEFAULT]);
    }
}
