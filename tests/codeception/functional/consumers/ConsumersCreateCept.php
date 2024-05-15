<?php

use app\models\City;
use perspectiva\phactory\FunctionalTester;
use tests\codeception\_pages\LoginPage;

/* @var $scenario Codeception\Scenario */
$I = new FunctionalTester($scenario);
LoginPage::signInAsAdmin($I);

// LegalPerson Física
$I->amOnPage('/consumers/create');
$person = Phactory::unsavedPhysicalPerson();
$legalPerson = Phactory::unsavedLegalPerson();
$consumer = Phactory::unsavedConsumer();
$I->fillField('Name', $person->name);
$I->fillField('CPF', $person->cpf);
$I->fillField('RG', $person->rg);
$I->fillField('Nationality', $person->nationality);
$I->selectOption('Occupation', $person->occupation_id);
$I->selectOption('Marital status', 'Single');
$I->fillField('Born on', $person->born_on);
$I->fillField('Email', $legalPerson->email);
$I->fillField('Cell Phone', $legalPerson->cell_number);
$I->fillField('Address', $legalPerson->address);
$I->fillField('District', $legalPerson->district);
$I->selectOption('City ID', City::find()->one()->id);
$I->fillField('Zipcode', $legalPerson->zip_code);
$I->click('Create');
$I->see('Consumer successfully created.');
$I->see($person->name);
$I->see($person->cpf);

// Dados inválidos
$I->amOnPage('/businesses/create');
$person = Phactory::unsavedPhysicalPerson();
$legalPerson = Phactory::unsavedLegalPerson();
$I->selectOption('form input[type=radio][name="LegalPerson[person_class]"]', 'PhysicalPerson');
$I->fillField('Name', '');
$I->click('Create');
$I->dontSee('Consumer successfully created.');
