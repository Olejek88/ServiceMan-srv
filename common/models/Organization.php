<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * Class Organization
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $title
 * @property string inn
 * @property string $secret
 * @property integer $createdAt
 * @property integer $changedAt
 */
class Organization extends ActiveRecord
{
    public const ORG_SERVICE_UUID = '0A39C01E-9915-4F8A-8A8A-96A28A5AC6DC';

    /**
     * Table name.
     *
     * @return string
     */
    public static function tableName()
    {
        return 'organization';
    }

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
                    'inn',
                    'secret',
                ],
                'required'
            ],
            [['createdAt', 'changedAt'], 'safe'],
            [['uuid',], 'string', 'max' => 45],
            [['title', 'inn', 'secret'], 'string', 'max' => 100],
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
            'title' => Yii::t('app', 'Наименование'),
            'inn' => Yii::t('app', 'ИНН'),
            'secret' => Yii::t('app', 'Секрет'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
        ];
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
//        return [
//            '_id',
//            'uuid',
//            'name',
//            'active',
//            'type',
//            'pin',
//            'user_id',
//            'contact',
//            'active',
//            'user' => function ($model) {
//                return $model->user;
//            },
//            'createdAt',
//            'changedAt',
//            'image',
//        ];
    }
}
