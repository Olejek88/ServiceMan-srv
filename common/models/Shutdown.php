<?php
namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "shutdown".
 *
 * @property integer $_id
 * @property string $oid идентификатор организации
 * @property string $uuid
 * @property string $startDate
 * @property string $endDate
 * @property string $comment
 * @property string $contragentUuid
 * @property string $createdAt
 * @property string $changedAt
 *
 * @property Contragent $contragent
 */
class Shutdown extends ActiveRecord
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
                'class' => TimestampBehavior::class,
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
        return 'shutdown';
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
                    'contragentUuid',
                    'startDate',
                    'endDate'
                ],
                'required'
            ],
            [['createdAt', 'changedAt'], 'safe'],
            [
                [
                    'uuid',
                    'contragentUuid'
                ],
                'string', 'max' => 45
            ],
        ];
    }

    /**
     * Fields
     *
     * @return array
     */
    public function fields()
    {
        return ['_id', 'uuid',
            'contragentUuid',
            'contragent' => function ($model) {
                return $model->contragent;
            },
            'startDate',
            'endDate',
            'createdAt', 'changedAt'
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
            'contragentUuid' => Yii::t('app', 'Контрагент'),
            'startDate' => Yii::t('app', 'Начало работ'),
            'endDate' => Yii::t('app', 'Окончание работ'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
        ];
    }

    /**
     * Объект связанного поля.
     *
     * @return \yii\db\ActiveRecord
     */
    public function getContragent()
    {
        $contragent = Contragent::find()
            ->select('*')
            ->where(['uuid' => $this->contragentUuid])
            ->one();
        return $contragent;
    }

    /**
     * Объект связанного поля.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getWorkStatus()
    {
        return $this->hasOne(
            WorkStatus::class, ['uuid' => 'workStatusUuid']
        );
    }

}
