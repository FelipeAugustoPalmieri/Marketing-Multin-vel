<?php

use perspectiva\phactory\FunctionalTester;
use tests\codeception\functional\businesses\BusinessesIndexPage;
use tests\codeception\_pages\LoginPage;

/* @var $scenario Codeception\Scenario */
$I = new FunctionalTester($scenario);
LoginPage::signInAsAdmin($I);

$consumer1 = Phactory::consumer([
   'paid_affiliation_fee' => TRUE
]);
$consumer2 = Phactory::consumer([
    'parentConsumer' => $consumer1,
    'sponsorConsumer' => $consumer1,
    'paid_affiliation_fee' => FALSE
]);

$I->amOnPage('/consumers');
$I->see('Consumers', 'h1');

$I->see($consumer1->legalPerson->person->name);
$I->see($consumer1->legalPerson->cell_number);
$I->see($consumer1->legalPerson->email);
$I->see('Yes');


$I->see($consumer2->legalPerson->person->name);
$I->see($consumer2->legalPerson->cell_number);
$I->see($consumer2->legalPerson->email);
$I->see('No');
