<?php
namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
/**
 * This is the model class for table "measure".
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $equipmentUuid
 * @property string $userUuid
 * @property double $value
 * @property string $date
 * @property string $createdAt
 * @property string $changedAt
 */

class Measure extends ActiveRecord
{

    /**
     * Behaviors
     *
     * @return array
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'createdAt',
                'updatedAtAttribute' => 'changedAt',
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * Название таблицы
     *
     * @inheritdoc
     *
     * @return string
     */
    public static function tableName()
    {
        return 'measure';
    }

    /**
     * Rules
     *
     * @inheritdoc
     *
     * @return array
     */
    public function rules()
    {
        return [
            [
                [
                    'uuid',
                    'equipmentUuid',
                    'userUuid',
                    'value',
                    'date'
                ],
                'required'
            ],
/*            [['photo'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg'],*/
            [['value'], 'number'],
            [['uuid', 'equipmentUuid', 'userUuid', 'date'], 'string', 'max' => 50],
            [['createdAt', 'changedAt'], 'safe'],
        ];
    }

    /**
     * Названия отрибутов
     *
     * @inheritdoc
     *
     * @return array
     */
    public function attributeLabels()
    {
        return [
            '_id' => Yii::t('app', '№'),
            'uuid' => Yii::t('app', 'Uuid'),
            'equipmentUuid' => Yii::t('app', 'Оборудование'),
            'userUuid' => Yii::t('app', 'Пользователь'),
            'value' => Yii::t('app', 'Значение'),
            'date' => Yii::t('app', 'Дата'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
        ];
    }

    /**
     * Fields
     *
     * @return array
     */
    public function fields()
    {
        return ['_id','uuid',
            'equipment' => function ($model) {
                return $model->equipment;
            },
            'equipmentUuid',
            'user' => function ($model) {
                return $model->user;
            },
            'userUuid',
            'value',
            'date',
            'createdAt',
            'changedAt',
        ];
    }

    /**
     * Проверка целостности модели?
     *
     * @return bool
     */
    public function upload()
    {
        if ($this->validate()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Объект связанного поля.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFlat()
    {
        return $this->hasOne(Flat::className(), ['uuid' => 'flatUuid']);
    }

    /**
     * Объект связанного поля.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['uuid' => 'userUuid']);
    }
}
