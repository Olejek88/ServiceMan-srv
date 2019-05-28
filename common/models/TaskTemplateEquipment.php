<?php
namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
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
            'period' => Yii::t('app', 'Периодичность'),
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
        if ($count<self::TASK_DEEP) {
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
        $next_dates="";
        $dates = explode(',', $this->next_dates);
        if ($dates) {
            $first = true;
            foreach ($dates as $date) {
                if (!$first)
                    $next_dates.=$date;
                $first = false;
            }
            $this->next_dates = $next_dates;
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
        $next_dates=$this->next_dates;
        $dates = explode(',', $this->next_dates);
        if ($dates) {
            $count = count($dates);
            while (self::TASK_DEEP - $count) {
                if ($count>0)
                    $last_date = strtotime($dates[$count-1]);
                else
                    $last_date = strtotime($this->last_date);
                $next_date = $last_date + $this->period*3600;
                $next_dates.=date("Y-m-d H:i:s",$next_date);
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
        $next_dates=$this->next_dates;
        $dates = explode(',', $this->next_dates);
        if ($dates && count($dates)>$index) {
            $dates[$index] = $date;
            $count = 0;
            while ($count<count($dates)) {
                if ($count>0) $next_dates.=',';
                $next_dates.=$dates[$count];
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
        $next_dates=$this->next_dates;
        $dates = explode(',', $this->next_dates);
        if ($dates && count($dates)>$index) {
            $count = 0;
            while ($count<count($dates)) {
                if ($count!=$index) {
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
