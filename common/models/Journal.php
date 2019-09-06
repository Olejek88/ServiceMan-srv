<?php

namespace common\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "journal".
 *
 * @property integer $_id
 * @property string $userUuid
 * @property string $description
 * @property string $date
 * @property string $type
 * @property string $title
 * @property string $referenceUuid
 *
 * @property Users $user
 */
class Journal extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'journal';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['userUuid', 'description'], 'required'],
            [['description', 'referenceUuid'], 'string'],
            [['date'], 'safe'],
            [['userUuid', 'type'], 'string', 'max' => 50],
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
            'description' => 'Описание',
            'date' => 'Дата',
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
}
