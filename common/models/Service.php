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
 * This is the model class for table "service".
 *
 * @category Category
 * @package  Common\models
 * @author   Oleg <olejek8@yandex.ru>
 * @license  http://www.yiiframework.com/license/ License name
 * @link     http://www.toirus.ru
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $service_name
 * @property string $title
 * @property integer $status
 * @property integer $delay
 * @property integer $active
 * @property string $last_start_date
 * @property string $last_stop_date
 * @property string $last_message
 * @property integer $last_message_type
 * @property string $createdAt
 * @property string $changedAt
 */
class Service extends ActiveRecord
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
        return 'service';
    }

    /**
     * Свойства объекта со связанными данными.
     *
     * @return array
     */
    public function fields()
    {
        return ['_id', 'uuid',
            'service_name',
            'title',
            'status',
            'delay',
            'active',
            'last_start_date',
            'last_stop_date',
            'last_message',
            'last_message_type',
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
                    'service_name',
                    'title'
                ],
                'required'
            ],
            [['last_start_date', 'last_stop_date', 'createdAt', 'changedAt'], 'safe'],
            [['status', 'active', 'delay', 'last_message_type'], 'number'],
            [
                [
                    'uuid',
                    'service_name'
                ],
                'string', 'max' => 50
            ],
            [['title', 'last_message'], 'string', 'max' => 100],
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
            'service_name' => Yii::t('app', 'Название сервиса'),
            'title' => Yii::t('app', 'Описание'),
            'status' => Yii::t('app', 'Статус'),
            'delay' => Yii::t('app', 'Задержка'),
            'active' => Yii::t('app', 'Активен'),
            'last_start_date' => Yii::t('app', 'Дата запуска'),
            'last_stop_date' => Yii::t('app', 'Дата останова'),
            'last_message' => Yii::t('app', 'Последнее сообщение'),
            'last_message_type' => Yii::t('app', 'Тип сообщения'),
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
     * Set service start date
     *
     * @param string $start_date
     * @return Service
     */
    public function setStatus($start_date){
        $this->last_start_date = $start_date;
        return $this;
    }
}
