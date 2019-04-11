<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\db\ActiveRecord;
/**
 * This is the model class for table "alarm".
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $comment
 * @property string $workStatusUuid
 * @property string $flatUuid
 * @property string $equipmentUuid
 * @property string $startDate
 * @property string $endDate
 * @property string $createdAt
 * @property string $changedAt
 */
class Task extends ActiveRecord
{
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
            [['uuid', 'workStatusUuid'], 'required'],
            [['comment'], 'string'],
            [['startDate', 'endDate', 'createdAt', 'changedAt'], 'safe'],
            [['uuid', 'workStatusUuid', 'flatUuid', 'equipmentUuid'], 'string', 'max' => 45],
        ];
    }

    public function fields()
    {
        return ['_id','uuid', 'comment',
            'workStatus' => function ($model) {
                return $model->workStatus;
            },
            'flatUuid', 'equipmentUuid',
            'startDate', 'endDate', 'createdAt', 'changedAt',
            'operations' => function ($model) {
                return $model->operations;
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
            'flatUuid' => Yii::t('app', 'Объект'),
            'equipmentUuid' => Yii::t('app', 'Оборудование'),
            'workStatusUuid' => Yii::t('app', 'Статус'),
            'startDate' => Yii::t('app', 'Начало'),
            'endDate' => Yii::t('app', 'Окончание'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
        ];
    }

    public function getFlat()
    {
        return $this->hasOne(Object::class, ['uuid' => 'flatUuid']);
    }

    public function getWorkStatus()
    {
        return $this->hasOne(WorkStatus::class, ['uuid' => 'workStatusUuid']);
    }

    public function getEquipment()
    {
        return $this->hasOne(
            Equipment::className(), ['uuid' => 'equipmentUuid']
        );
    }

    public function getOperations()
    {
        return $this->hasMany(Operation::class, ['taskUuid' => 'uuid']);
    }
}
