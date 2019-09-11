<?php

namespace common\models;

use common\components\MainFunctions;
use Cron\CronExpression;
use Yii;
use yii\base\InvalidConfigException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Exception;
use yii\db\Expression;

/**
 * This is the model class for table "task_template_equipment".
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $taskTemplateUuid
 * @property string $equipmentUuid
 * @property string $period
 * @property string $last_date
 * @property string $next_dates
 * @property string $createdAt
 * @property string $changedAt
 *
 * @property Equipment $equipment
 * @property TaskTemplate $taskTemplate
 * @property Users $user
 * @property string[] $dates
 */
class TaskTemplateEquipment extends ActiveRecord
{
    const TASK_DEEP = 20;

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
     * Название таблицы.
     *
     * @return string
     *
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'task_template_equipment';
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
            [
                [
                    'uuid',
                    'taskTemplateUuid',
                    'equipmentUuid',
                ],
                'required'
            ],
            [['createdAt', 'changedAt', 'last_date', 'next_dates'], 'safe'],
            [['uuid'], 'string', 'max' => 50],
            [
                [
                    'taskTemplateUuid',
                    'equipmentUuid',
                    'period'
                ],
                'string',
                'max' => 45
            ],
            [
                ['equipmentUuid'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Equipment::class,
                'targetAttribute' => ['equipmentUuid' => 'uuid']
            ],
            [
                ['taskTemplateUuid'],
                'exist',
                'skipOnError' => true,
                'targetClass' => TaskTemplate::class,
                'targetAttribute' => ['taskTemplateUuid' => 'uuid']
            ],
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
            'taskTemplateUuid' => Yii::t('app', 'Шаблон задачи'),
            'taskTemplate' => Yii::t('app', 'Шаблон задачи'),
            'equipmentUuid' => Yii::t('app', 'Оборудование'),
            'equipment' => Yii::t('app', 'Оборудование'),
            'period' => Yii::t('app', 'Периодичность (дн.)'),
            'last_date' => Yii::t('app', 'Дата последнего запуска'),
            'next_dates' => Yii::t('app', 'Даты следующих запусков'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
        ];
    }

    /**
     * Link
     *
     * @return ActiveQuery
     */
    public function getEquipment()
    {
        return $this->hasOne(
            Equipment::class, ['uuid' => 'equipmentUuid']
        );
    }

    /**
     * Link
     *
     * @return ActiveQuery
     */
    public function getTaskTemplate()
    {
        return $this->hasOne(
            TaskTemplate::class, ['uuid' => 'taskTemplateUuid']
        );
    }

    /**
     * Ищем пользователя который может выполнить эту задачу
     *
     * @return array|ActiveRecord
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function getUser()
    {
        $equipmentSystem = $this->equipment['equipmentType']['equipmentSystem'];
        $house = $this->equipment['object']['house'];
        $userHouses = UserHouse::find()->where(['houseUuid' => $house['uuid']])->all();
        foreach ($userHouses as $userHouse) {
            $userSystems = UserSystem::find()->where(['userUuid' => $userHouse['userUuid']])->all();
            // если в специализации пользователя есть нужная - выберем пользователя по-умолчанию
            foreach ($userSystems as $userSystem) {
                if ($equipmentSystem['uuid'] == $userSystem['equipmentSystemUuid']) {
                    $user = Users::find()
                        ->where(['uuid' => $userHouse['userUuid']])
                        ->andWhere(['active' => 1])
                        ->one();
                    if ($user)
                        return $user;
                }
            }
        }
        return null;
    }

    /**
     * Link
     *
     * @return string[]
     */
    public function getDates()
    {
        return explode(',', $this->next_dates);
    }

    /**
     * Link
     *
     * @param $date
     * @return boolean
     */
    public function pushDate($date)
    {
        $count = 0;
        $dates = explode(',', $this->next_dates);
        if ($dates)
            $count = count($dates);
        if ($count < self::TASK_DEEP) {
            $this->next_dates .= $date;
            return true;
        }
        return false;
    }

    /**
     * Link
     * @return string
     */
    public function popDate()
    {
        $next_dates = "";
        $dates = explode(',', $this->next_dates);
        MainFunctions::log('task.log', $this->next_dates);
        if ($dates) {
            $first = 0;
            foreach ($dates as $date) {
                if ($first == 1)
                    $next_dates .= $date;
                if ($first > 1)
                    $next_dates .= "," . $date;
                $first++;
            }
            $this->next_dates = $next_dates;
            MainFunctions::log('task.log', $next_dates);
            $this->save();
            return $dates[0];
        }
        return false;
    }

    /**
     * Link
     * @return boolean
     */
    public function formDates()
    {
        $next_dates = $this->next_dates;
        $dates = explode(',', $this->next_dates);
        if ($dates) {
            $count = count($dates);
            if (strlen($this->next_dates) < 6) $count = 0;

            while (self::TASK_DEEP - $count) {
                if ($count > 0)
                    $last_date = strtotime($dates[$count - 1]);
                else
                    $last_date = strtotime($this->last_date);
                if (($last_date + 3600) < time())
                    $this->popDate();

                if ($count > 0) {
                    $next_dates .= ',';
                    if (is_numeric($this->period)) {
                        $next_date = $last_date + $this->period * 24 * 3600;
                        $next_dates .= date("Y-m-d 00:00:00", $next_date);
                        $dates[$count] = date("Y-m-d 00:00:00", $next_date);
                    } else {
                        $cron = CronExpression::factory($this->period);
                        $next_date = $cron->getNextRunDate(date("Y-m-d 00:00:00", $last_date));
                        $next_dates .= $next_date->format('Y-m-d 00:00:00');
                        $dates[$count] = $next_date->format('Y-m-d 00:00:00');
                    }
                } else {
                    $next_dates .= date("Y-m-d 00:00:00", $last_date);
                    $dates[$count] = date("Y-m-d 00:00:00", $last_date);
                }
                $count++;
            }
            $this->next_dates = $next_dates;
            $this->save();
            return true;
        }
        return false;
    }

    /**
     * Link
     * @param $date
     * @param $index
     * @return boolean
     */
    public function changeDate($date, $index)
    {
        $next_dates = $this->next_dates;
        $dates = explode(',', $this->next_dates);
        if ($dates && count($dates) > $index) {
            $dates[$index] = $date;
            $count = 0;
            while ($count < count($dates)) {
                if ($count > 0) $next_dates .= ',';
                $next_dates .= $dates[$count];
                $count++;
            }
            $this->next_dates = $next_dates;
            $this->save();
            return true;
        }
        return false;
    }

    /**
     * Link
     * @param $index
     * @return boolean
     */
    public function removeDate($index)
    {
        $next_dates = $this->next_dates;
        $dates = explode(',', $this->next_dates);
        if ($dates && count($dates) > $index) {
            $count = 0;
            while ($count < count($dates)) {
                if ($count != $index) {
                    if ($count > 0) $next_dates .= ',';
                    $next_dates .= $dates[$count];
                }
                $count++;
            }
            $this->next_dates = $next_dates;
            $this->save();
            return true;
        }
        return false;
    }
}
