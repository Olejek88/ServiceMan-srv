<?php

namespace api\models\form;

use api\models\User;
use yii\base\Model;
use yii\web\UnauthorizedHttpException;

/**
 * Class LoginForm
 * @package api\models\form
 */
class LoginForm extends Model
{
    /**
     * @var string $login
     */
    public $login;
    /**
     * @var string $pin
     */
    public $pin;
    /**
     * @var User
     */
    protected $user;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['login', 'pin'], 'required'],
            ['pin', 'validatePin'],
        ];
    }

    /**
     * @param $attribute
     * @param $params
     *
     * @throws UnauthorizedHttpException
     */
    public function validatePin($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();

            if (!$user
                || !$user->validatePin($this->pin)
//                || !$user->role
//              || !in_array($user->role, User::$back_allowed_roles)
            ) {
//                $this->addError($attribute, Yii::t('app', 'error password'));
                throw new UnauthorizedHttpException();
            }
        }
    }

    /**
     * @return User|null
     */
    public function getUser()
    {
        if ($this->user === null) {
            $this->user = User::findByLogin($this->login);
        }

        return $this->user;
    }
}