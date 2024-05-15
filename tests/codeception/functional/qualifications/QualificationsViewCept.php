<?php

use perspectiva\phactory\FunctionalTester;
use tests\codeception\_pages\LoginPage;

/* @var $scenario Codeception\Scenario */
$I = new FunctionalTester($scenario);
LoginPage::signInAsAdmin($I);

$qualification = Phactory::qualification();

$I->amOnPage('/qualifications/view?id=' . $qualification->id);
$I->see($qualification->description);
$I->see('2,50%');
$I->see($qualification->position);

$I->amOnPage('/qualifications/view?id=0');
$I->seePageNotFound();
