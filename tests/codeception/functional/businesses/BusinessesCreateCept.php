<?php

use app\models\City;
use perspectiva\phactory\FunctionalTester;
use tests\codeception\_pages\LoginPage;

/* @var $scenario Codeception\Scenario */
$I = new FunctionalTester($scenario);
LoginPage::signInAsAdmin($I);

// LegalPerson Física
$I->amOnPage('/businesses/create');
$person = Phactory::unsavedPhysicalPerson();
$legalPerson = Phactory::unsavedLegalPerson();
$I->selectOption('form input[type=radio][name="LegalPerson[person_class]"]', 'PhysicalPerson');
$I->fillField('Name', $person->name);
$I->fillField('CPF', $person->cpf);
$I->fillField('Email', $legalPerson->email);
$I->fillField('Cell Phone', $legalPerson->cell_number);
$I->fillField('Address', $legalPerson->address);
$I->fillField('District', $legalPerson->district);
$I->selectOption('City ID', City::find()->one()->id);
$I->fillField('Zipcode', $legalPerson->zip_code);
$I->click('Create');
$I->see('Business successfully created.');
$I->see($person->name);
$I->see($person->cpf);

// LegalPerson Jurídica
$I->amOnPage('/businesses/create');
$person = Phactory::unsavedJuridicalPerson();
$legalPerson = Phactory::unsavedLegalPerson();
$I->selectOption('form input[type=radio][name="LegalPerson[person_class]"]', 'JuridicalPerson');
$I->fillField('Trading name', $person->trading_name);
$I->fillField('Company name', $person->company_name);
$I->fillField('Contact name', $person->contact_name);
$I->fillField('CNPJ', $person->cnpj);
$I->fillField('Email', $legalPerson->email);
$I->fillField('Cell Phone', $legalPerson->cell_number);
$I->fillField('Address', $legalPerson->address);
$I->fillField('District', $legalPerson->district);
$I->selectOption('City ID', City::find()->one()->id);
$I->fillField('Zipcode', $legalPerson->zip_code);
$I->click('Create');
$I->see('Business successfully created.');
$I->see($person->trading_name);
$I->see($person->company_name);
$I->see($person->contact_name);
$I->see($person->cnpj);

// Dados inválidos
$I->amOnPage('/businesses/create');
$person = Phactory::unsavedPhysicalPerson();
$legalPerson = Phactory::unsavedLegalPerson();
$I->selectOption('form input[type=radio][name="LegalPerson[person_class]"]', 'PhysicalPerson');
$I->fillField('Name', '');
$I->click('Create');
$I->dontSee('Business successfully created.');
