<?php

namespace common\models;

/**
 * Class UserTokenAuth
 * @package common\models
 *
 * @property integer $type
 */
class UserTokenAuth extends UserToken
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['last_access'], 'required'],
            [
                ['valid_till'],
                'default',
                'value' => function () {
                    return date(DATE_W3C, strtotime('+1 week'));
                }
            ],
            [['token'], 'string', 'max' => 32],
            [
                ['user_id', 'token'],
                'unique',
                'targetAttribute' => ['user_id', 'token'],
                'message' => 'The combination of User ID and Token has already been taken.'
            ],
            [['type'], 'default', 'value' => self::AUTH_TYPE],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                $this->setAttribute('token', \Yii::$app->security->generateRandomString());
            }
            return true;
        }
        return false;
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