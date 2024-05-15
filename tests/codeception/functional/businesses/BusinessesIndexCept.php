<?php

use perspectiva\phactory\FunctionalTester;
use tests\codeception\_pages\LoginPage;

/* @var $scenario Codeception\Scenario */
$I = new FunctionalTester($scenario);
LoginPage::signInAsAdmin($I);

$businessPhysicalPerson = Phactory::business('physicalPerson');
$businessJuridicalPerson = Phactory::business('juridicalPerson');

$I->amOnPage('/businesses');
$I->see('Businesses', 'h1');


$I->see($businessJuridicalPerson->getName());
$I->see($businessJuridicalPerson->getEconomicActivity());
$I->see($businessJuridicalPerson->getPhoneNumber());

$I->see($businessJuridicalPerson->getName());
$I->see($businessJuridicalPerson->getEconomicActivity());
$I->see($businessJuridicalPerson->getPhoneNumber());
