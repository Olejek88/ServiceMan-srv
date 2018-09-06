<?php

namespace api\models;

use api\helpers\Html;

/**
 * Class User
 * @package api\models
 */
class User extends \common\models\User
{
    /**
     * Запрос на регистрацию
     * @param string $email
     * @param string $password
     * @return static
     * @throws \Exception
     */
    public static function requestSignup(string $email, string $password)
    {
        $user = new static();
        $user->username = $user->email = $email;
        $user->setPassword($password);
        $user->generateAuthKey();
        if (!$user->save()) {
            throw new \DomainException('User create error: ' . strip_tags(Html::errorSummary([$user])));
        }

        return $user;
    }
}
