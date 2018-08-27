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
 * This is the model class for table "external_event".
 *
 * @category Category
 * @package  Common\models
 * @link     http://www.toirus.ru
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $tagUuid
 * @property string $actionUuid
 * @property string $date
 * @property integer $status
 * @property integer $verdict
 * @property string $createdAt
 * @property string $changedAt
 */

class ExternalEvent extends ActiveRecord
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
        return 'external_event';
    }

    /**
     * Свойства объекта со связанными данными.
     *
     * @return array
     */
    public function fields()
    {
        return ['_id', 'uuid', 'date', 'status', 'verdict',
            'action' => function ($model) {
                return $model->title;
            },
            'tag' => function ($model) {
                return $model->tag;
            },
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
                    'tagUuid',
                    'date'
                ],
                'required'
            ],
            [['createdAt', 'changedAt'], 'safe'],
            [
                [
                    'uuid',
                    'tagUuid',
                    'actionUuid',
                    'date'
                ],
                'string', 'max' => 50
            ],
            [['status', 'verdict'], 'integer'],
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
            'tagUuid' => Yii::t('app', 'Тег'),
            'actionUuid' => Yii::t('app', 'UUID действия'),
            'date' => Yii::t('app', 'Дата'),
            'status' => Yii::t('app', 'Статус'),
            'verdict' => Yii::t('app', 'Вердикт'),
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
     * Объект связанного поля.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getExternalTag()
    {
        return $this->hasOne(
            ExternalTag::className(), ['uuid' => 'tagUuid']
        );

    }
    /**
     * Объект связанного поля.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getActionType()
    {
        return $this->hasOne(
            ActionType::className(), ['uuid' => 'actionUuid']
        );
    }

}
