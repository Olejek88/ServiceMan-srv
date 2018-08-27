<?php
/**
 * PHP Version 7.0
 *
 * @category Category
 * @package  Common\models
 * @author   Максим Шумаков <ms.profile.d@gmail.com>
 * @license  http://www.yiiframework.com/license/ License name
 * @link     http://www.toirus.ru
 */

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "messages".
 *
 * @category Category
 * @package  Common\models
 * @link     http://www.toirus.ru
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $fromUserUuid
 * @property string $toUserUuid
 * @property string $text
 * @property string $date
 * @property integer $status
 * @property string $createdAt
 * @property string $changedAt
 */

class Message extends ActiveRecord
{
    /**
     * Behaviors.
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
     * Table name.
     *
     * @return string
     */
    public static function tableName()
    {
        return 'messages';
    }

    /**
     * Свойства объекта со связанными данными.
     *
     * @return array
     */
    public function fields()
    {
        return ['_id', 'uuid', 'date', 'text', 'status',
            'fromUserUuid' => function ($model) {
                return $model->name;
            },
            'toUserUuid' => function ($model) {
                return $model->name;
            },
            'createdAt', 'changedAt'
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
                    'fromUserUuid',
                    'toUserUuid',
                    'text',
                    'status',
                    'date'
                ],
                'required'
            ],
            [['createdAt', 'changedAt'], 'safe'],
            [
                [
                    'uuid',
                    'fromUserUuid',
                    'toUserUuid',
                    'date'
                ],
                'string', 'max' => 50
            ],
            [['status'], 'integer'],
            [['text'], 'string'],
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
            'fromUserUuid' => Yii::t('app', 'Отправитель'),
            'toUserUuid' => Yii::t('app', 'Получатель'),
            'date' => Yii::t('app', 'Дата'),
            'status' => Yii::t('app', 'Статус'),
            'text' => Yii::t('app', 'Текст'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
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
    public function getFromUser()
    {
        return $this->hasOne(
            Users::className(), ['uuid' => 'fromUserUuid']
        );
    }

    /**
     * Объект связанного поля.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getToUser()
    {
        return $this->hasOne(
            Users::className(), ['uuid' => 'toUserUuid']
        );
    }
}
