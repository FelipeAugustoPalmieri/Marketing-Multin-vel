<?php

use perspectiva\phactory\FunctionalTester;
use tests\codeception\_pages\LoginPage;

/* @var $scenario Codeception\Scenario */
$I = new FunctionalTester($scenario);
LoginPage::signInAsAdmin($I);

$I->amOnPage('/planes/create');
$plane = Phactory::unsavedPlane();
$I->fillField('Name Plane', $plane->name_plane);
$I->fillField('Multiplier', $plane->multiplier);
$I->fillField('Goal Points', $plane->goal_points);
$I->click('Create');
$I->see('Plane successfully created.');

// Dados inválidos
$I->amOnPage('/planes/create');
$plane = Phactory::unsavedPlane();
$I->fillField('Name Plane', '');
$I->fillField('Multiplier', '');
$I->fillField('Goal Points', '');
$I->click('Create');
$I->dontSee('Plane successfully created.');
