<?php

use perspectiva\phactory\FunctionalTester;
use tests\codeception\_pages\LoginPage;

$I = new FunctionalTester($scenario);
LoginPage::signInAsAdmin($I);

Phactory::plane(['name_plane' => 'PlaneOne']);
Phactory::plane(['name_plane' => 'PlaneTwo']);
Phactory::plane(['name_plane' => 'PlaneThree']);

$I->amOnPage('/planes');
$I->see('PlaneOne');
$I->see('PlaneTwo');
$I->see('PlaneThree');
