<?php

use perspectiva\phactory\FunctionalTester;
use tests\codeception\_pages\LoginPage;

$I = new FunctionalTester($scenario);
LoginPage::signInAsAdmin($I);

Phactory::qualification(['description' => 'Teste']);
Phactory::qualification(['description' => 'Two']);
Phactory::qualification(['description' => 'Three']);

$I->amOnPage('/qualifications');
$I->see('Teste');
$I->see('Two');
$I->see('Three');
