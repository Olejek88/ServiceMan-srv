<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\db\ActiveRecord;
/**
 * This is the model class for table "external_system".
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $title
 * @property string $address
 * @property integer $port
 * @property string $interface
 * @property string $login
 * @property string $pass
 * @property string $createdAt
 * @property string $changedAt
 */

class ExternalSystem extends ActiveRecord
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
        return 'external_system';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uuid', 'interface', 'title', 'address'], 'required'],
            [['createdAt', 'changedAt'], 'safe'],
            [['port'], 'integer'],
            [
                [
                    'uuid',
                    'login',
                    'pass',
                    'interface',
                    'address'
                ],
                'string', 'max' => 45
            ],
            [['title'], 'string', 'max' => 100],
        ];
    }


    public function fields()
    {
        return ['_id', 'uuid', 'title', 'address', 'port', 'interface', 'login', 'pass', 'createdAt', 'changedAt'];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            '_id' => Yii::t('app', '№'),
            'uuid' => Yii::t('app', 'Uuid'),
            'title' => Yii::t('app', 'Название системы'),
            'address' => Yii::t('app', 'Адрес хоста'),
            'port' => Yii::t('app', 'Порт'),
            'interface' => Yii::t('app', 'Интерфейс'),
            'login' => Yii::t('app', 'Логин'),
            'pass' => Yii::t('app', 'Пароль'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен')
        ];
    }

    public function upload()
    {
        if ($this->validate()) {
            return true;
        } else {
            return false;
        }
    }
}
