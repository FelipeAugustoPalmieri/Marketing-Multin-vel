<?php

use perspectiva\phactory\FunctionalTester;
use tests\codeception\_pages\LoginPage;

/* @var $scenario Codeception\Scenario */
$I = new FunctionalTester($scenario);
LoginPage::signInAsAdmin($I);

$I->amOnPage('/configurations/create');
$configuration = Phactory::unsavedConfiguration();
$I->fillField('Name', $configuration->name);
$I->selectOption('Type', $configuration->type);
$I->fillField('Value', $configuration->value);
$I->click('Create');
$I->see('Configuration successfully created.');

// Dados inválidos
$I->amOnPage('/configurations/create');
$configuration = Phactory::unsavedConfiguration();
$I->fillField('Name', '');
$I->fillField('Value', '');
$I->click('Create');
$I->dontSee('Configuration successfully created.');
