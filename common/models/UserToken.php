<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "{{%user_token}}".
 *
 * @property int $id
 * @property string $oid идентификатор организации
 * @property int $user_id
 * @property string $token
 * @property string $valid_till
 * @property int $status
 * @property string $last_access
 * @property string $created_at
 * @property string $updated_at
 *
 * @property User $user
 */
class UserToken extends ActiveRecord
{
    const AUTH_TYPE = 1;
    const PASSWORD_RESET_TYPE = 2;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user_token}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'token', 'valid_till', 'last_access', 'created_at', 'updated_at'], 'required'],
            [['user_id', 'status'], 'integer'],
            [['valid_till', 'last_access', 'created_at', 'updated_at'], 'safe'],
            [['token'], 'string', 'max' => 32],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => '_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'user_id' => Yii::t('app', 'User ID'),
            'token' => Yii::t('app', 'Token'),
            'valid_till' => Yii::t('app', 'Valid Till'),
            'status' => Yii::t('app', 'Status'),
            'last_access' => Yii::t('app', 'Last Access'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['_id' => 'user_id']);
    }

    /**
     * дергаем время последнего доступа по токену, продлеваем его на неделю.
     */
    public function touchLastAccess()
    {
        $this->valid_till = date(DATE_W3C, strtotime('+1 week'));
        $this->last_access = new Expression('CURRENT_TIMESTAMP');
        $this->save(false, ['last_access', 'valid_till']);
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return time() < strtotime($this->valid_till);
    }

    /**
     * @inheritdoc
     */
    public function afterFind()
    {
        parent::afterFind();

        $this->touchLastAccess();
    }

}
