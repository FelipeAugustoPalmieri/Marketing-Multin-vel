<?php

use perspectiva\phactory\FunctionalTester;
use tests\codeception\_pages\LoginPage;

/* @var $scenario Codeception\Scenario */
$I = new FunctionalTester($scenario);
LoginPage::signInAsAdmin($I);

$consumer = Phactory::consumer();
$legalPerson = Phactory::unsavedPhysicalPerson();
$I->amOnPage('/consumers/update?id=' . $consumer->id);
$I->fillField('Name', $legalPerson->name);
$I->click('Update');
$I->see('Consumer successfully updated.');
$I->see($legalPerson->name);

$I->amOnPage('/consumers/update?id=' . $consumer->id);
$I->fillField('Name', '');
$I->click('Update');
$I->dontSee('Consumer successfully updated.');
