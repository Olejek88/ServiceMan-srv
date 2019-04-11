<?php

namespace common\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "receipt".
 *
 * @property integer $_id
 * @property string $contragentUuid
 * @property string $userUuid
 * @property string $requestUuid
 * @property string $description
 * @property string $result
 * @property boolean $closed
 * @property string $date
 *
 * @property Request $request
 * @property Contragent $contragent
 * @property Users $user
 */
class Receipt extends ActiveRecord
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
            [['userUuid', 'contragentUuid', 'date', 'description'], 'required'],
            [['description', 'userUuid', 'contragentUuid', 'result', 'description'], 'string'],
            [['date'], 'safe'],
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
            'userUuid' => 'Uuid Пользователя',
            'description' => 'Описание',
            'date' => 'Дата',
        ];
    }

    /**
     * Объект связанного поля.
     *
     * @return \yii\db\ActiveQuery
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
     * @return \yii\db\ActiveQuery
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
     * @return \yii\db\ActiveQuery
     */
    public function getRequest()
    {
        return $this->hasOne(
            Request::class, ['uuid' => 'requestUuid']
        );
    }
}
