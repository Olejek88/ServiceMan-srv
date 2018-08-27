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
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "event_attribute_type".
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $eventUuid
 * @property string $attributeTypeUuid
 * @property string $createdAt
 * @property string $changedAt
 */

class EventAttributeType extends ActiveRecord
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
     * Название таблицы.
     *
     * @return string
     *
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'event_attribute_type';
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
                    'eventUuid',
                    'attributeTypeUuid',
                ],
                'required'
            ],
            [['createdAt', 'changedAt'], 'safe'],
            [['uuid'], 'string', 'max' => 50],
            [
                [
                    'eventUuid',
                    'attributeTypeUuid',
                ],
                'string',
                'max' => 45
            ],
            [
                ['eventUuid'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Event::className(),
                'targetAttribute' => ['eventUuid' => 'uuid']
            ],
            [
                ['attributeTypeUuid'],
                'exist',
                'skipOnError' => true,
                'targetClass' => AttributeType::className(),
                'targetAttribute' => ['attributeTypeUuid' => 'uuid']
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
            'eventUuid' => Yii::t('app', 'Событие'),
            'attributeTypeUuid' => Yii::t('app', 'Тип аттрибута'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
        ];
    }

    /**
     * Link
     *
     * @return ActiveQuery
     */
    public function getEvent()
    {
        return $this->hasOne(
            Event::className(), ['uuid' => 'eventUuid']
        );
    }

    /**
     * Link
     *
     * @return ActiveQuery
     */
    public function getAttributeType()
    {
        return $this->hasOne(
            AttributeType::className(), ['uuid' => 'attributeTypeUuid']
        );
    }
}
