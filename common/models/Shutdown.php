<?php

namespace common\models;

use common\components\ZhkhActiveRecord;
use Yii;
use yii\base\InvalidConfigException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Exception;
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
class Shutdown extends ZhkhActiveRecord
{
    public const DESCRIPTION = 'Планируемые отключения';

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
                    'startDate'
                ],
                'required'
            ],
            [['oid', 'createdAt', 'changedAt', 'comment'], 'safe'],
            [
                [
                    'uuid',
                    'contragentUuid'
                ],
                'string', 'max' => 45
            ],
            [['oid'], 'exist', 'targetClass' => Organization::class, 'targetAttribute' => ['oid' => 'uuid']],
            [['oid'], 'checkOrganizationOwn'],
        ];
    }

    /**
     * Fields
     *
     * @return array
     */
    public function fields()
    {
        $fields = parent::fields();
        return $fields;
//        return ['_id', 'uuid',
//            'contragentUuid',
//            'contragent' => function ($model) {
//                return $model->contragent;
//            },
//            'startDate',
//            'endDate',
//            'createdAt', 'changedAt'
//        ];
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
            'comment' => Yii::t('app', 'Комментарий'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
        ];
    }

    /**
     * Объект связанного поля.
     *
     * @return ActiveRecord
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function getContragent()
    {
        $contragent = Contragent::find()
            ->select('*')
            ->where(['uuid' => $this->contragentUuid])
            ->one();
        return $contragent;
    }

    function getActionPermissions()
    {
        return array_merge_recursive(parent::getActionPermissions(), [
            'read' => [
                'form',
            ],
            'edit' => [
                'new',
            ]]);
    }
}
