<?php

use perspectiva\phactory\FunctionalTester;
use tests\codeception\_pages\LoginPage;

/* @var $scenario Codeception\Scenario */
$I = new FunctionalTester($scenario);
$plane = Phactory::plane();
LoginPage::signInAsAdmin($I);

$I->amOnPage('/planes/update?id=' . $plane->id);
$I->fillField('Name Plane', 'Another Name');
$I->fillField('Multiplier', $plane->multiplier);
$I->fillField('Goal Points', $plane->goal_points);
$I->click('Update');
$I->see('Plane successfully updated.');
$I->see('Another Name');

// Invalid data
$I->amOnPage('/planes/update?id=' . $plane->id);
$I->fillField('Name Plane', '');
$I->fillField('Multiplier', '');
$I->fillField('Goal Points', '');
$I->click('Update');
$I->dontSee('Plane successfully updated.');
