<?php

namespace backend\tests\functional;

use backend\tests\FunctionalTester;
use common\fixtures\User as UserFixture;

/**
 * Class CheckView
 */
class CheckCest
{
    public function _before(FunctionalTester $I)
    {
    }

    /**
     * @param FunctionalTester $I
     */
    public function site(FunctionalTester $I)
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
