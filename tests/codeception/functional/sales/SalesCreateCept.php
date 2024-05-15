<?php

use app\models\Sales;
use perspectiva\phactory\FunctionalTester;
use tests\codeception\_pages\LoginPage;

/* @var $scenario Codeception\Scenario */
$I = new FunctionalTester($scenario);
LoginPage::signInAsAdmin($I);
$consumer = Phactory::consumer();
$business = Phactory::business();
$consumable = Phactory::consumable([
    'business' => $business,
]);

$I->amOnPage('/sales/create');
$I->selectOption('Consumer ID', $consumer->id);
$I->selectOption('Business ID', $business->id);
$I->selectOption('Consumable ID', $consumable->id);
$I->fillField('Invoice Code', 'ABC123');
$I->fillField('Total', 100);
$I->click('Create');
$I->see('Sale successfully created.');
