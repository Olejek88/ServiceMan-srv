<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "settings".
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $title
 * @property string $parameter
 * @property string $createdAt
 * @property string $changedAt
 */
class Settings extends ActiveRecord
{
    const SETTING_TASK_PAUSE_BEFORE_WARNING = 'B8FB4706-E48F-422A-9B73-6750B7491BF0';
    const SETTING_SHOW_WARNINGS = 'EA1B3D23-CFE4-4EE0-8091-9EEC27E60E95';
    const SETTING_GPS_DEEP = '04B72195-6285-46FB-955D-4D4BE223DD9C';
    // адрес с которого приходят уведомления от интерсвязи
    const SETTING_IS_IP = '97F69939-B4C1-4CB0-9547-DFA88F2E39B9';

    /**
     * Table name.
     *
     * @return string
     */
    public static function tableName()
    {
        return 'settings';
    }

    public static function storeSetting($uuid, $parameter)
    {
        $settings = Settings::find()
            ->where(['uuid' => $uuid])
            ->one();
        if ($settings) {
            $settings['parameter'] = $parameter;
            $settings->save();
        } else {
            $settings = new Settings();
            $settings->title = 'Нет описания параметра!';
            if ($uuid == Settings::SETTING_TASK_PAUSE_BEFORE_WARNING) {
                $settings->title = 'Время на получение задачи до выдачи предупреждения';
            }
            if ($uuid == Settings::SETTING_SHOW_WARNINGS) {
                $settings->title = 'Показывать предупреждения в таблице задач';
            }
            $settings->uuid = $uuid;
            $settings->parameter = $parameter;
            $settings->save();
        }
    }

    public static function getSettings($uuid)
    {
        $settings = Settings::find()
            ->where(['uuid' => $uuid])
            ->one();
        if ($settings) {
            return $settings['parameter'];
        }
        return 0;
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
     * Свойства объекта со связанными данными.
     *
     * @return array
     */
    public function fields()
    {
        return ['_id', 'uuid', 'title', 'parameter', 'createdAt', 'changedAt'];
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
                    'parameter',
                ],
                'required'
            ],
            [['createdAt', 'changedAt'], 'safe'],
            [
                [
                    'uuid',
                    'title',
                    'parameter'
                ],
                'string', 'max' => 50
            ]
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
            'title' => Yii::t('app', 'Название'),
            'parameter' => Yii::t('app', 'Парметр'),
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
}
