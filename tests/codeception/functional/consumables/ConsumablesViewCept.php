<?php

use perspectiva\phactory\FunctionalTester;
use tests\codeception\_pages\LoginPage;

/* @var $scenario Codeception\Scenario */
$I = new FunctionalTester($scenario);
LoginPage::signInAsAdmin($I);
$business = Phactory::business('physicalPerson');
$consumable = Phactory::consumable([
    'business' => $business,
]);

$I->amOnPage('/consumable/view?id=' . $consumable->id . '&=businessId= ' . $business->id);
$I->see($consumable->id);
$I->see($consumable->description);
$I->see('2,50%');

$I->amOnPage('/businesses/view?id=0&businessId= ' . $business->id);;
$I->seePageNotFound();
