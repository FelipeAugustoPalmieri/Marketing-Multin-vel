<?php

use perspectiva\phactory\FunctionalTester;
use tests\codeception\_pages\LoginPage;

/* @var $scenario Codeception\Scenario */
$I = new FunctionalTester($scenario);
$configuration = Phactory::configuration();
LoginPage::signInAsAdmin($I);

$I->amOnPage('/configurations/update?id=' . $configuration->id);
$I->fillField('Name', 'Another Name');
$I->selectOption('Type', $configuration->type);
$I->fillField('Value', $configuration->value);
$I->click('Update');
$I->see('Configuration successfully updated.');
$I->see('Another Name');

// Invalid data
$I->amOnPage('/configurations/update?id=' . $configuration->id);
$I->fillField('Name', '');
$I->fillField('Value', '');
$I->click('Update');
$I->dontSee('Configuration successfully updated.');
