<?php

namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * User model
 *
 * @property integer $_id
 * @property string $oid идентификатор организации
 * @property string $username
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property string $auth_key
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property int $id
 * @property string $authKey
 * @property string $password write-only password
 *
 * @property Users $users
 */
class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;

    const ROLE_ADMIN = 'admin';
    const ROLE_OPERATOR = 'operator';
    const ROLE_ANALYST = 'analyst';
    const ROLE_USER = 'user';

    const PERMISSION_ADMIN = 'permissionAdmin';
    const PERMISSION_OPERATOR = 'permissionOperator';
    const PERMISSION_ANALYST = 'permissionAnalyst';
    const PERMISSION_USER = 'permissionUser';

    /**
     * Table name.
     *
     * @inheritdoc
     *
     * @return string
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * Behaviors.
     *
     * @inheritdoc
     *
     * @return array
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * Rules.
     *
     * @inheritdoc
     *
     * @return array
     */
    public function rules()
    {
        return [
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],
        ];
    }

    /**
     * Поиск пользователя по id и статусу.
     *
     * @param integer $id Ид пользователя.
     *
     * @inheritdoc
     *
     * @return User
     */
    public static function findIdentity($id)
    {
        return static::findOne(['_id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Поиск пользователя по accessToken.
     *
     * @param string $token Токен.
     * @param string $type Тип.
     *
     * @inheritdoc
     *
     * @return User
     * @throws NotSupportedException
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        $userToken = UserToken::findOne(['token' => $token]);
        if ($userToken != null && $userToken->isValid()) {
            return User::findOne($userToken->user_id);
        } else {
            return null;
        }
    }

    /**
     * Finds user by username
     *
     * @param string $username Имя/логин пользователя.
     *
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(
            ['username' => $username, 'status' => self::STATUS_ACTIVE]
        );
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     *
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne(
            [
                'password_reset_token' => $token,
                'status' => self::STATUS_ACTIVE,
            ]
        );
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     *
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int)substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * Get id.
     *
     * @inheritdoc
     *
     * @return integer
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * Get authKey.
     *
     * @inheritdoc
     *
     * @return string
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * Проверка токена на достоверность.
     *
     * @param string $authKey Токен.
     *
     * @inheritdoc
     *
     * @return boolean
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     *
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app
            ->security
            ->validatePassword($password, $this->password_hash);
    }

    /**
     * Validates pin
     *
     * @param string $pin pin to validate
     *
     * @return bool if pin provided is valid for current user
     */
    public function validatePin($pin)
    {
        $users = Users::findOne(['user_id' => $this->_id]);
//        return Yii::$app->security->validatePassword($pin, $users->pin);
        return $pin == $users->pin;
    }

    /**
     * Generates password hash from password and sets it to the model
     * @param string $password Пароль.
     *
     * @return void
     * @throws \Exception
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     *
     * @return void
     * @throws \Exception
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     *
     * @return void
     *
     * @throws \Exception
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security
                ->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     *
     * @return void
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    /**
     * @param null $duration
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function generateAccessToken($duration = null)
    {
        if ($duration === null) {
            $duration = \Yii::$app->params['duration']['week'];
        }

        $token = \Yii::createObject([
            'class' => UserTokenAuth::class,
            'valid_till' => date(DATE_W3C, time() + $duration),
//            'type' => UserTokenAuth::AUTH_TYPE,
        ]);

        $token->link('user', $this);


        return $token->token;
    }

    /**
     * Finds user by username or email
     *
     * @param string $login
     * @return array|User|null|ActiveRecord
     */
    public static function findByLogin($login)
    {
        return static::find()
            ->where([
                'and',
                ['or', ['username' => $login], ['email' => $login]],
                'status' => self::STATUS_ACTIVE,
            ])
            ->one();
    }

    /**
     * Finds user by Users->uuid
     *
     * @param string $usersUuid
     * @return array|User|null|ActiveRecord
     */
    public static function findByUuid($usersUuid)
    {
        $users = Users::findOne(['uuid' => $usersUuid]);
        if ($users != null) {
            return static::find()
                ->where([
                    '_id' => $users->user_id,
                    'status' => self::STATUS_ACTIVE,
                ])->one();
        } else {
            return null;
        }
    }

    /**
     * @return null|Users
     */
    public function getUsers() {
        return Users::findOne(['user_id' => $this->_id]);
    }
}
