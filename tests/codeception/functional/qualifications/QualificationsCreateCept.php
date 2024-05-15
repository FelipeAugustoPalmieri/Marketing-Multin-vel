<?php

use perspectiva\phactory\FunctionalTester;
use tests\codeception\_pages\LoginPage;

/* @var $scenario Codeception\Scenario */
$I = new FunctionalTester($scenario);
LoginPage::signInAsAdmin($I);

$I->amOnPage('/qualifications/create');
$qualification = Phactory::unsavedQualification();
$I->fillField('Description', $qualification->description);
$I->fillField('Gain Percentage', $qualification->gain_percentage);
$I->fillField('Position', $qualification->position);
$I->click('Create');
$I->see('Qualification successfully created.');

// Dados inválidos
$I->amOnPage('/qualifications/create');
$qualification = Phactory::unsavedQualification();
$I->fillField('Description', '');
$I->fillField('Position', '');
$I->click('Create');
$I->dontSee('Qualification successfully created.');
