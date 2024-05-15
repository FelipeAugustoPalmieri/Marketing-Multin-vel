<?php

use perspectiva\phactory\FunctionalTester;
use tests\codeception\_pages\LoginPage;

/* @var $scenario Codeception\Scenario */
$I = new FunctionalTester($scenario);
LoginPage::signInAsAdmin($I);

$consumer = Phactory::consumer();

$I->amOnPage('/consumers/view?id=' . $consumer->id);
$I->see($consumer->id);
$I->see($consumer->legalPerson->person->name);
$I->see($consumer->legalPerson->person->cpf);

$I->amOnPage('/businesses/view?id=0');
$I->seePageNotFound();
