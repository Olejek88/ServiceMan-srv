<?php

namespace backend\models;

use common\components\MainFunctions;
use common\components\ReferenceFunctions;
use common\models\Organization;
use common\models\User;
use common\models\Users;
use yii\base\Model;
use Exception;
use Throwable;
use Yii;

/**
 * Signup form
 */
class SignupForm extends Model
{
    public $username;
    public $email;
    public $password;
    public $organizationTitle;
    public $organizationInn;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['username', 'trim'],
            ['username', 'required'],
            ['username', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This username has already been taken.'],
            ['username', 'string', 'min' => 2, 'max' => 255],

            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This email address has already been taken.'],

            ['password', 'required'],
            ['password', 'string', 'min' => 6],

            ['organizationTitle', 'trim'],
            ['organizationTitle', 'required'],
            ['organizationTitle', 'string', 'min' => 2, 'max' => 100],

            ['organizationInn', 'trim'],
            ['organizationInn', 'required'],
            ['organizationInn', 'string', 'min' => 2, 'max' => 100],
        ];
    }

    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     * @throws Exception
     * @throws Throwable
     */
    public
    function signup()
    {
        if (!$this->validate()) {
            return null;
        }

        $user = new User();
        $user->username = $this->username;
        $user->email = $this->email;
        $user->setPassword($this->password);
        $user->generateAuthKey();

        if ($user->save()) {
            $organization = new Organization();
            $organization->uuid = MainFunctions::GUID();
            $organization->title = $this->organizationTitle;
            $organization->inn = $this->organizationInn;
            $organization->secret = $this->randomString(8);
            if ($organization->save()) {
                $users = new Users();
                $users->uuid = MainFunctions::GUID();
                $users->user_id = $user->_id;
                $users->type = 0;
                $users->active = 1;
                $users->name = $user->username;
                $users->pin = 'PIN:' . $this->password;
                $users->contact = 'нет';
                $users->oid = $organization->uuid;
                if ($users->save()) {
                    $am = Yii::$app->getAuthManager();
                    $roleAdmin = $am->getRole(User::ROLE_ADMIN);
                    $am->assign($roleAdmin, $user->_id);
                    // создаём набор данных для новой организации
                    ReferenceFunctions::loadReferences($organization->uuid);
                    ReferenceFunctions::loadReferencesNext($organization->uuid);
                    return $user;
                } else {
                    $user->delete();
                    $organization->delete();
                    return null;
                }
            } else {
                $user->delete();
                return null;
            }
        } else {
            return null;
        }
    }

    /*
     * Create a random string
     * @author	XEWeb <>
     * @param $length the length of the string to create
     * @return $str the string
     */
    function randomString($length = 6)
    {
        $str = "";
        $characters = array_merge(range('A', 'Z'), range('a', 'z'), range('0', '9'));
        $max = count($characters) - 1;
        for ($i = 0; $i < $length; $i++) {
            $rand = mt_rand(0, $max);
            $str .= $characters[$rand];
        }
        return $str;
    }
}
