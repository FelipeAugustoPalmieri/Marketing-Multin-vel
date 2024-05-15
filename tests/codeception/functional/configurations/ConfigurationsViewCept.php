<?php

use perspectiva\phactory\FunctionalTester;
use tests\codeception\_pages\LoginPage;

/* @var $scenario Codeception\Scenario */
$I = new FunctionalTester($scenario);
LoginPage::signInAsAdmin($I);

$configuration = Phactory::configuration();

$I->amOnPage('/configurations/view?id=' . $configuration->id);
$I->see($configuration->name);
$I->see($configuration->type);
$I->see($configuration->value);

$I->amOnPage('/configurations/view?id=0');
$I->seePageNotFound();
