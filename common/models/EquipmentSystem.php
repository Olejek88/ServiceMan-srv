<?php
namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "equipment_system".
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $title
 * @property string $titleUser
 * @property string $createdAt
 * @property string $changedAt
 */
class EquipmentSystem extends ActiveRecord
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
        return 'equipment_system';
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
            [['uuid', 'titleUser', 'title'], 'required'],
            [['createdAt', 'changedAt'], 'safe'],
            [['uuid', 'titleUser'], 'string', 'max' => 50],
            [['title'], 'string', 'max' => 100],
        ];
    }


    /**
     * Fields.
     *
     * @return array
     *
     * @inheritdoc
     */
    public function fields()
    {
        return ['_id', 'uuid', 'titleUser', 'title', 'createdAt', 'changedAt'];
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
            'title' => Yii::t('app', 'Название'),
            'titleUser' => Yii::t('app', 'Специализация'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
        ];
    }

    /**
     * Upload.
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
