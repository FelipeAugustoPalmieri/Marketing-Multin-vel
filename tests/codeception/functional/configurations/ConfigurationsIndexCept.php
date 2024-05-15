<?php

use perspectiva\phactory\FunctionalTester;
use tests\codeception\_pages\LoginPage;

$I = new FunctionalTester($scenario);
LoginPage::signInAsAdmin($I);

Phactory::configuration(['name' => 'Config1']);
Phactory::configuration(['name' => 'Config2']);
Phactory::configuration(['name' => 'Config3']);

$I->amOnPage('/configurations');
$I->see('Config1');
$I->see('Config2');
$I->see('Config3');
