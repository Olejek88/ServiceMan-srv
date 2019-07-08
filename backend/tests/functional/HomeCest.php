<?php

namespace frontend\tests\functional;

use backend\tests\FunctionalTester;

class HomeCest
{
    /**
     * @param FunctionalTester $I
     */
    public function checkOpen(FunctionalTester $I)
    {
        $I->amOnPage('/dashboard');
        $I->see('Сервис обслуживания');
        $I->amOnPage('/files');
        $I->see('Сервис обслуживания');
        $I->amOnPage('/');
        $I->see('Сервис обслуживания');
        $I->amOnPage('/timeline');
        $I->see('Сервис обслуживания');
    }
}