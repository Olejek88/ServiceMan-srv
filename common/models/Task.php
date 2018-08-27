<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\db\ActiveRecord;
/**
 * This is the model class for table "task".
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $comment
 * @property string $orderUuid
 * @property string $taskVerdictUuid
 * @property string $taskStatusUuid
 * @property string $taskTemplateUuid
 * @property string $startDate
 * @property string $endDate
 * @property integer $prevCode
 * @property integer $nextCode
 * @property string $createdAt
 * @property string $changedAt
 */
class Task extends ActiveRecord
{
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
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'task';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uuid', 'taskVerdictUuid', 'taskStatusUuid', 'taskTemplateUuid'], 'required'],
            [['comment'], 'string'],
            [['startDate', 'endDate', 'createdAt', 'changedAt'], 'safe'],
            [['prevCode', 'nextCode'], 'integer'],
            [['uuid', 'orderUuid', 'taskVerdictUuid', 'taskStatusUuid', 'taskTemplateUuid'], 'string', 'max' => 45],
        ];
    }

    public function fields()
    {
        return ['_id','uuid', 'comment', 'orderUuid',
            'taskVerdict' => function ($model) {
                return $model->taskVerdict;
            },
            'taskStatus' => function ($model) {
                return $model->taskStatus;
            },
            'taskTemplate' => function ($model) {
                return $model->taskTemplate;
            },
            'prevCode', 'nextCode', 'startDate', 'endDate', 'createdAt', 'changedAt',
            'stages' => function ($model) {
                return $model->stages;
            },
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
            'comment' => Yii::t('app', 'Комментарий'),
            'orderUuid' => Yii::t('app', 'Наряд'),
            'taskVerdictUuid' => Yii::t('app', 'Вердикт'),
            'taskStatusUuid' => Yii::t('app', 'Статус'),
            'taskTemplateUuid' => Yii::t('app', 'Шаблон'),
            'startDate' => Yii::t('app', 'Начальная дата'),
            'endDate' => Yii::t('app', 'Конечная дата'),
            'prevCode' => Yii::t('app', 'Предыдущий код'),
            'nextCode' => Yii::t('app', 'Следующий код'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
        ];
    }

    public function getOrder()
    {
        return $this->hasOne(Orders::className(), ['uuid' => 'orderUuid']);
    }

    public function getTaskStatus()
    {
        return $this->hasOne(TaskStatus::className(), ['uuid' => 'taskStatusUuid']);
    }

    public function getTaskVerdict()
    {
        return $this->hasOne(
            TaskVerdict::className(), ['uuid' => 'taskVerdictUuid']
        );
    }

    public function getTaskTemplate()
    {
        return $this->hasOne(
            TaskTemplate::className(), ['uuid' => 'taskTemplateUuid']
        );
    }

    public function getTaskFullName()
    {
        $taskTemplate = $this->hasOne(
            TaskTemplate::className(), ['uuid' => 'taskTemplateUuid']
        )->one();
        return $taskTemplate['title'].' ['.$this->createdAt.']';
    }

    public function getStages()
    {
        return $this->hasMany(Stage::className(), ['taskUuid' => 'uuid']);
    }

    public function parseObject($objectParse, $uuid = null, $prop = [])
    {
        $result = [];
        if ($objectParse !== null) {
            $array = [];

            foreach ($objectParse as $value => $property) {
                $array[$value] = $property;
            }

            if (!empty($prop)) {

                // TODO: Оптимизировать решение
                if (isset($array['authorUuid'])) {
                    $array['authorUuid'] = $prop[0];
                }

                if (isset($array['userUuid'])) {
                    $array['userUuid'] = $prop[1];
                }

                if (isset($array['orderStatusUuid'])) {
                    $array['orderStatusUuid'] = $prop[2];
                }

                if (isset($array['orderVerdictUuid'])) {
                    $array['orderVerdictUuid'] = $prop[3];
                }

                if (isset($array['orderLevelUuid'])) {
                    $array['orderLevelUuid'] = $prop[4];
                }

            }

            if ($uuid !== null) {

                $result = (object) $array;

            }
        }

        return $result;
    }
}
