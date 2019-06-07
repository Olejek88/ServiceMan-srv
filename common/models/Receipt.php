<?php

namespace common\models;

use common\components\ZhkhActiveRecord;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "receipt".
 *
 * @property integer $_id
 * @property string $oid идентификатор организации
 * @property string $uuid
 * @property string $contragentUuid
 * @property string $userUuid
 * @property string $requestUuid
 * @property string $description
 * @property string $result
 * @property boolean $closed
 * @property string $date
 * @property string $createdAt
 * @property string $changedAt
 *
 * @property Request $request
 * @property Contragent $contragent
 * @property Users $user
 */
class Receipt extends ZhkhActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'receipt';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uuid', 'userUuid', 'contragentUuid', 'date', 'description'], 'required'],
            [['description', 'userUuid', 'contragentUuid', 'result'], 'string'],
            [['date','closed','oid'], 'safe'],
            [['userUuid', 'contragentUuid'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            '_id' => '№',
            'userUuid' => 'Пользователь',
            'user' => 'Пользователь',
            'contragentUuid' => 'Контрагент',
            'contragent' => 'Контрагент',
            'description' => 'Описание',
            'result' => 'Результат',
            'closed' => 'Закрыта',
            'date' => 'Дата'
        ];
    }

    /**
     * Объект связанного поля.
     *
     * @return ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(
            Users::class, ['uuid' => 'userUuid']
        );
    }

    /**
     * Объект связанного поля.
     *
     * @return ActiveQuery
     */
    public function getContragent()
    {
        return $this->hasOne(
            Contragent::class, ['uuid' => 'contragentUuid']
        );
    }

    /**
     * Объект связанного поля.
     *
     * @return ActiveQuery
     */
    public function getRequest()
    {
        return $this->hasOne(
            Request::class, ['uuid' => 'requestUuid']
        );
    }
}
