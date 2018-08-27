<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\db\ActiveRecord;
/**
 * This is the model class for table "defect".
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $userUuid
 * @property string $date
 * @property string $equipmentUuid
 * @property string $defectTypeUuid
 * @property boolean $process
 * @property string $comment
 * @property string $taskUuid
 * @property string $createdAt
 * @property string $changedAt
 */
class Defect extends ActiveRecord
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
        return 'defect';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uuid', 'date', 'equipmentUuid', 'process'], 'required'],
            [['createdAt', 'changedAt'], 'safe'],
            [['uuid', 'defectTypeUuid', 'taskUuid', 'userUuid'], 'string', 'max' => 45],
            [['comment'], 'string', 'max' => 100],
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
            'userUuid' => Yii::t('app', 'Пользователь'),
            'date' => Yii::t('app', 'Дата фиксации'),
            'equipmentUuid' => Yii::t('app', 'Оборудование'),
            'defectTypeUuid' => Yii::t('app', 'Тип дефекта'),
            'process' => Yii::t('app', 'Обработано'),
            'comment' => Yii::t('app', 'Комментарий'),
            'taskUuid' => Yii::t('app', 'Задача'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
        ];
    }

    public function fields()
    {
        return [
            '_id',
            'uuid',
            'user' => function ($model) {
                return $model->user;
            },
            'date',
            'equipment' => function ($model) {
                return $model->equipment;
            },
            'defectType' => function ($model) {
                return $model->defectType;
            },
            'process',
            'comment',
            'task' => function ($model) {
                return $model->task;
            },
            'createdAt',
            'changedAt',
        ];
    }

    public function getDefectType()
    {
        return $this->hasOne(DefectType::className(), ['uuid' => 'defectTypeUuid']);
    }
    public function getEquipment()
    {
        return $this->hasOne(Equipment::className(), ['uuid' => 'equipmentUuid']);
    }
    public function getUser()
    {
        return $this->hasOne(Users::className(), ['uuid' => 'userUuid']);
    }
    public function getTask()
    {
        return $this->hasOne(Task::className(), ['uuid' => 'taskUuid']);
    }

}
