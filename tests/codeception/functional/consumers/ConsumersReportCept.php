<?php

use perspectiva\phactory\FunctionalTester;
use tests\codeception\_pages\LoginPage;
use app\models\Sale;
/* @var $scenario Codeception\Scenario */

//admin
$I = new FunctionalTester($scenario);
LoginPage::signInAsAdmin($I);

Phactory::qualification([
    'description' => 'Consumidor',
    'gain_percentage' => 11,
    'position' => 11,
    'completed_levels' => 1,
    'register_network_sale' => false,
]);
Phactory::qualification([
    'description' => 'Empreendedor',
    'gain_percentage' => 8,
    'position' => 10,
    'completed_levels' => 3,
    'register_network_sale' => true,
]);

$plane = Phactory::plane(['multiplier' => 0.6]);

$parent = Phactory::consumer(['plane_id' => $plane->id]);
$filho = Phactory::consumer([
    'parentConsumer' => $parent,
    'sponsorConsumer' => $parent,
    'plane_id' => $plane->id
]);

//empresas
$business = Phactory::business([
    'legalPerson' => Phactory::legalPerson([
        'person_class' => 'JuridicalPerson',
        'person_id' => Phactory::juridicalPerson(['trading_name' => 'Perspectiva'])->id,
    ]),
]);

//venda
$sale = new Sale;
$sale->consumer_id = $parent->id;
$sale->business_id = $business->id;
$sale->consumable_id = Phactory::consumable()->id;
$sale->invoice_code = '123456';
$sale->total = 150;
$sale->save();

$I->amOnPage('/consumers/report');
$I->see('Perspectiva');


//consumidor que ve a venda

$I = new FunctionalTester($scenario);
$parent = LoginPage::signInAsConsumer($I);

Phactory::qualification([
    'description' => 'Consumidor',
    'gain_percentage' => 11,
    'position' => 11,
    'completed_levels' => 1,
    'register_network_sale' => false,
]);
Phactory::qualification([
    'description' => 'Empreendedor',
    'gain_percentage' => 8,
    'position' => 10,
    'completed_levels' => 3,
    'register_network_sale' => true,
]);

$filho = Phactory::consumer([
    'parentConsumer' => $parent,
    'sponsorConsumer' => $parent,
    'plane_id' => $parent->plane->id
]);

//empresas
$business = Phactory::business([
    'legalPerson' => Phactory::legalPerson([
        'person_class' => 'JuridicalPerson',
        'person_id' => Phactory::juridicalPerson(['trading_name' => 'Perspectiva'])->id,
    ]),
]);

//venda
$sale = new Sale;
$sale->consumer_id = $parent->id;
$sale->business_id = $business->id;
$sale->consumable_id = Phactory::consumable()->id;
$sale->invoice_code = '123456';
$sale->total = 150;
$sale->save();

$I->amOnPage('/consumers/report');
$I->see('Perspectiva');

//consumidor que ve a venda

$I = new FunctionalTester($scenario);
$parent = LoginPage::signInAsConsumer($I);

Phactory::qualification([
    'description' => 'Consumidor',
    'gain_percentage' => 11,
    'position' => 11,
    'completed_levels' => 1,
    'register_network_sale' => false,
]);
Phactory::qualification([
    'description' => 'Empreendedor',
    'gain_percentage' => 8,
    'position' => 10,
    'completed_levels' => 3,
    'register_network_sale' => true,
]);

$filho = Phactory::consumer([
    'parentConsumer' => $parent,
    'sponsorConsumer' => $parent,
    'plane_id' => $parent->plane->id
]);

//empresas
$business = Phactory::business([
    'legalPerson' => Phactory::legalPerson([
        'person_class' => 'JuridicalPerson',
        'person_id' => Phactory::juridicalPerson(['trading_name' => 'Perspectiva'])->id,
    ]),
]);

//venda
$sale = new Sale;
$sale->consumer_id = $filho->id;
$sale->business_id = $business->id;
$sale->consumable_id = Phactory::consumable()->id;
$sale->invoice_code = '123456';
$sale->total = 150;
$sale->save();

$I->amOnPage('/consumers/report');
$I->dontSee('Perspectiva');
