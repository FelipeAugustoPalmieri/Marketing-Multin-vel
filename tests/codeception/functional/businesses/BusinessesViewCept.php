<?php

use perspectiva\phactory\FunctionalTester;
use tests\codeception\_pages\LoginPage;

/* @var $scenario Codeception\Scenario */
$I = new FunctionalTester($scenario);
LoginPage::signInAsAdmin($I);

$businessPhysicalPerson = Phactory::business('physicalPerson');
$businessJuridicalPerson = Phactory::business('juridicalPerson');
$physicalUser = Phactory::user([
    'authenticable_type' => 'Business',
    'authenticable_id' => $businessPhysicalPerson->id,
]);
$juridicalUser = Phactory::user([
    'authenticable_type' => 'Business',
    'authenticable_id' => $businessJuridicalPerson->id,
]);

$I->amOnPage('/businesses/view?id=' . $businessPhysicalPerson->id);
$I->see($businessPhysicalPerson->id);
$I->see($businessPhysicalPerson->legalPerson->person->name);
$I->see($businessPhysicalPerson->legalPerson->person->cpf);
$I->see($physicalUser->email);

$I->amOnPage('/businesses/view?id=' . $businessJuridicalPerson->id);
$I->see($businessJuridicalPerson->id);
$I->see($businessJuridicalPerson->legalPerson->person->trading_name);
$I->see($businessJuridicalPerson->legalPerson->person->company_name);
$I->see($businessJuridicalPerson->legalPerson->person->cnpj);
$I->see($juridicalUser->email);

$I->amOnPage('/businesses/view?id=0');
$I->seePageNotFound();
