<?php

namespace common\models;

use common\components\ZhkhActiveRecord;
use Yii;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "user_contragent".
 *
 * @property integer $_id
 * @property string $oid идентификатор организации
 * @property string $uuid
 * @property string $userUuid
 * @property string $contragentUuid
 * @property string $createdAt
 * @property string $changedAt
 *
 * @property ActiveQuery $user
 * @property ActiveQuery $contragent
 */
class UserContragent extends ZhkhActiveRecord
{
    public const DESCRIPTION = 'Связь пользователей с контрагентами';

    /**
     * Название таблицы.
     * @return string
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_contragent';
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
            [['uuid', 'userUuid', 'contragentUuid'], 'required'],
            [['createdAt', 'changedAt'], 'safe'],
            [['uuid', 'userUuid', 'contragentUuid'], 'string', 'max' => 50],
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
            'userUuid' => Yii::t('app', 'Пользователь'),
            'contragent' => Yii::t('app', 'Контрагент'),
            'contragentUuid' => Yii::t('app', 'Контрагент'),
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
    public function getContragent()
    {
        return $this->hasOne(Contragent::class, ['uuid' => 'contragentUuid']);
    }
}
