<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "clients".
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $name
 * @property string $description
 * @property string $phone
 * @property string $createdAt
 * @property string $changedAt
 */
class Clients extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'clients';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uuid', 'name', 'description', 'phone'], 'required'],
            [['description'], 'string'],
            [['createdAt', 'changedAt'], 'safe'],
            [['uuid'], 'string', 'max' => 50],
            [['name', 'phone'], 'string', 'max' => 150],
        ];
    }


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            '_id' => Yii::t('app', '№'),
            'uuid' => Yii::t('app', 'Uuid'),
            'name' => Yii::t('app', 'Имя'),
            'description' => Yii::t('app', 'Описание'),
            'phone' => Yii::t('app', 'Телефон'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
        ];
    }
}
