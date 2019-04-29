<?php
namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "operation_template".
 *
 * @property integer $_id
 * @property string $oid идентификатор организации
 * @property string $uuid
 * @property string $title
 * @property string $description
 * @property string $createdAt
 * @property string $changedAt
 */
class OperationTemplate extends ActiveRecord
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
        return 'operation_template';
    }

    /**
     * Rules
     *
     * @inheritdoc
     *
     * @return $mixes
     */
    public function rules()
    {
        return [
            [
                [
                    'uuid',
                    'title',
                    'description'
                ],
                'required'
            ],
            [['description'], 'string'],
            [['createdAt', 'changedAt'], 'safe'],
            [
                ['uuid','oid'], 'string', 'max' => 45
            ],
            [['title'], 'string', 'max' => 200],
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
            'title', 'description',
            'createdAt', 'changedAt'
        ];
    }


    /**
     * Attribute labels
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
            'title' => Yii::t('app', 'Название'),
            'description' => Yii::t('app', 'Описание'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
        ];
    }

    /**
     * Upload
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
