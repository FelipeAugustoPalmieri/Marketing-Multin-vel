<?php

use perspectiva\phactory\FunctionalTester;
use tests\codeception\_pages\LoginPage;

/* @var $scenario Codeception\Scenario */
$I = new FunctionalTester($scenario);
LoginPage::signInAsAdmin($I);

$business = Phactory::business('physicalPerson');
$legalPerson = Phactory::unsavedPhysicalPerson();
$I->amOnPage('/businesses/update?id=' . $business->id);
$I->fillField('Name', $legalPerson->name);
$I->click('Update');
$I->see('Business successfully updated.');
$I->see($legalPerson->name);

$business = Phactory::business('juridicalPerson');
$legalPerson = Phactory::unsavedJuridicalPerson();
$I->amOnPage('/businesses/update?id=' . $business->id);
$I->fillField('Trading name', $legalPerson->trading_name);
$I->fillField('Company name', $legalPerson->company_name);
$I->fillField('Contact name', $legalPerson->contact_name);
$I->click('Update');
$I->see('Business successfully updated.');
$I->see($legalPerson->trading_name);
$I->see($legalPerson->company_name);
$I->see($legalPerson->contact_name);

$I->amOnPage('/businesses/update?id=' . $business->id);
$I->fillField('Trading name', '');
$I->click('Update');
$I->dontSee('Business successfully updated.');
