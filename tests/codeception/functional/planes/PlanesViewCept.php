<?php

use perspectiva\phactory\FunctionalTester;
use tests\codeception\_pages\LoginPage;

/* @var $scenario Codeception\Scenario */
$I = new FunctionalTester($scenario);
LoginPage::signInAsAdmin($I);

$plane = Phactory::plane();

$I->amOnPage('/planes/view?id=' . $plane->id);
$I->see($plane->name_plane);
$I->see($plane->multiplier);
$I->see($plane->goal_points);

$I->amOnPage('/planes/view?id=0');
$I->seePageNotFound();
