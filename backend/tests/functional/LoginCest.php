<?php

namespace backend\tests\functional;

use backend\tests\FunctionalTester;

class LoginCest
{
    function _before(FunctionalTester $I)
    {
        $I->amOnRoute('site/login');
    }

    protected function formParams()
    {
        return [
            'LoginForm[username]' => 'dev',
            'LoginForm[password]' => 'qwerty',
            'LoginForm[rememberMe]' => true
        ];
    }

    public function checkValidLogin(FunctionalTester $I)
    {
        $I->submitForm('#login-form', $this->formParams(),'login-button');
        $I->seeInTitle('Сводная');
        $I->dontSee('Неверное имя пользователя');
        $I->dontSee('<h3>Not Found 404</h3>');
    }
}
