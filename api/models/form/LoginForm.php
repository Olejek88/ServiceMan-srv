<?php

namespace api\models\form;

use api\models\User;
use common\models\Users;
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

            if (!$user || !$user->validatePin($this->pin)) {
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
            $this->user = User::findByUuid($this->login);
            if ($this->user->users->type != Users::USERS_WORKER) {
                return null;
            }
        }

        return $this->user;
    }
}
