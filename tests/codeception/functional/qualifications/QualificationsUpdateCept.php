<?php

use perspectiva\phactory\FunctionalTester;
use tests\codeception\_pages\LoginPage;

/* @var $scenario Codeception\Scenario */
$I = new FunctionalTester($scenario);
$qualification = Phactory::qualification();
LoginPage::signInAsAdmin($I);

$I->amOnPage('/qualifications/update?id=' . $qualification->id);
$I->fillField('Description', 'Another Description');
$I->fillField('Gain Percentage', 1);
$I->fillField('Position', $qualification->position);
$I->click('Update');
$I->see('Qualification successfully updated.');
$I->see('Another Description');

// Invalid data
$I->amOnPage('/qualifications/update?id=' . $qualification->id);
$I->fillField('Description', '');
$I->fillField('Position', '');
$I->click('Update');
$I->dontSee('Qualification successfully updated.');
