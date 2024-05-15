<?php
namespace tests\unit\models;

use app\models\Test;
use app\models\Consumer;
use app\models\Configuration;
use app\models\TransactionDetail;
use app\models\SalesReport;
use app\models\SalesRepresentativeCity;
use Phactory;
use perspectiva\phactory\ActiveRecordTest;
use tests\FakerTrait;
use yii\db\ActiveQuery;
use app\models\Sale;

class SaleTest extends ActiveRecordTest
{
    use FakerTrait;

    protected $parent;
    protected $filhoBBBB;
    protected $filhoABAAA;

    public function testCalculatePoints()
    {
        Phactory::qualification(['completed_levels' => 1]);

        $parent = Phactory::consumer();
        $plane = Phactory::plane(['multiplier' => 0.6]);
        $filho = Phactory::consumer([
            'parentConsumer' => $parent,
            'sponsorConsumer' => $parent,
            'plane' => $plane
        ]);

        $consumable = Phactory::consumable(['shared_percentage' => 15]);

        $sale = Phactory::Sale([
            'total' => 1000,
            'consumer' => $filho,
            'consumable' => $consumable,
        ]);

        $this->assertEquals(90.00, $sale->calculatePoints());
    }

    public function testCalculateFees()
    {
        Phactory::qualification(['completed_levels' => 1]);

        $parent = Phactory::consumer();
        $plane = Phactory::plane(['multiplier' => 0.6]);
        $filho = Phactory::consumer([
            'parentConsumer' => $parent,
            'sponsorConsumer' => $parent,
            'plane' => $plane
        ]);

        $consumable = Phactory::consumable(['shared_percentage' => 15]);

        $sale = Phactory::sale([
            'total' => 1000,
            'consumer' => $filho,
            'consumable' => $consumable,
        ]);

        $this->assertEquals(150.00, $sale->calculateFees());
    }


    public function testSale()
    {
        $sale = new Sale;
        $sale->total = 1000;
        $sale->invoice_code = '123abc';
        $sale->consumer_id = Phactory::consumer()->id;
        $sale->business_id = Phactory::business()->id;
        $sale->consumable_id = Phactory::consumable()->id;

        $this->assertTrue($sale->save());

        $this->assertNotNull($sale->points);
        $this->assertNotNull($sale->fees);
        $this->assertNotNull($sale->shared_percentage);
        $this->assertNotNull($sale->plane_multiplier);
        $this->assertNotNull($sale->plane_id);
        $this->assertNotNull($sale->unshared_fees);
    }

    public function testCalcProfitValue()
    {
        $this->generateTree();
        $business = Phactory::business();
        $consumable = Phactory::consumable([
            'business' => $business,
            'shared_percentage' => 1.5]);


        $sale = new Sale;
        $sale->total = 1000;
        $sale->invoice_code = '123abc';
        $sale->consumer_id = $this->filhoAAAA->id;;
        $sale->business_id = $business->id;
        $sale->consumable_id = $consumable->id;

        $this->assertTrue($sale->save());

        $this->assertEquals(2, TransactionDetail::find()->count());
        //valores conferidos com Debug::debug(TransactionDetail::find()->all());
    }

    public function testSaleTree()
    {
        $this->generateTree();

        $sale = new Sale;
        $sale->total = 100;
        $sale->invoice_code = '123abc';
        $sale->consumer_id = $this->filhoBBBB->id;
        $sale->business_id = Phactory::business()->id;
        $sale->consumable_id = Phactory::consumable()->id;

        $this->assertTrue($sale->save());

        $this->assertEquals(1, TransactionDetail::find()->count());
    }

    public function testSaleTreeWithoutRepresentativeCity()
    {
        $this->generateTree();
        $consumable = Phactory::consumable(['shared_percentage' => 5]);
        $representative = Phactory::SalesRepresentativeCity([
            'sales_representative_id' => $this->parent->id,
            'city_id' => $this->filhoBBBB->legalPerson->city->id
        ]);
        $sale = new Sale;
        $sale->total = 100;
        $sale->invoice_code = '123abc';
        $sale->consumer_id = $this->filhoBBBB->id;
        $sale->business_id = Phactory::business()->id;
        $sale->consumable_id = $consumable->id;

        $this->assertTrue($sale->save());

        $this->assertEquals(1, TransactionDetail::find()->count());
    }

    public function testSaleTreeWithRepresentativeCityOnix()
    {
        $this->generateTree();
        $consumable = Phactory::consumable(['shared_percentage' => 5]);
        $business = Phactory::business([
                'id' => 1,
            ]);
        $representative = Phactory::SalesRepresentativeCity([
            'sales_representative_id' => $this->parent->id,
            'city_id' => $this->filhoBBBB->legalPerson->city->id
        ]);

        $sale = new Sale;
        $sale->total = 100;
        $sale->invoice_code = '123abc';
        $sale->consumer_id = $this->filhoBBBB->id;
        $sale->business_id = $business->id;
        $sale->consumable_id = $consumable->id;

        $this->assertTrue($sale->save());

        $this->assertEquals(2, TransactionDetail::find()->count());
    }

    public function testSaleTreeWithRepresentativeCity()
    {
        $this->generateTree();
        $consumable = Phactory::consumable(['shared_percentage' => 5]);
        $business = Phactory::business();
        $representative = Phactory::SalesRepresentativeCity([
            'sales_representative_id' => $this->parent->id,
            'city_id' => $business->legalPerson->city->id
        ]);
        $sale = new Sale;
        $sale->total = 100;
        $sale->invoice_code = '123abc';
        $sale->consumer_id = $this->filhoBBBB->id;
        $sale->business_id = $business->id;
        $sale->consumable_id = $consumable->id;

        $this->assertTrue($sale->save());

        $this->assertEquals(2, TransactionDetail::find()->count());
    }

    public function testGetRepresentativeOnix()
    {
        $this->generateTree();
        $consumable = Phactory::consumable(['shared_percentage' => 5]);
        $business = Phactory::business();
        $s = Phactory::SalesRepresentativeCity([
            'sales_representative_id' => $this->parent->id,
            'city_id' => $this->filhoBBBB->legalPerson->city_id
        ]);

        $configuracao = Configuration::find()
            ->where(['id' => Configuration::ID_CONVENIO_BUSINESS])
            ->one();
        $configuracao->value = $business->id;
        $this->assertTrue($configuracao->save());

        $sale = new Sale;
        $sale->total = 100;
        $sale->invoice_code = '123abc';
        $sale->consumer_id = $this->filhoBBBB->id;
        $sale->business_id = $business->id;
        $sale->consumable_id = $consumable->id;

        $this->assertTrue($sale->save());

        $this->assertEquals(
            $this->filhoBBBB->legalPerson->city->attributes,
            $sale->getRepresentativeCity()->attributes
        );
    }

    public function testGetRepresentativeNotOnix()
    {
        $this->generateTree();
        $consumable = Phactory::consumable(['shared_percentage' => 5]);
        $business = Phactory::business();
        Phactory::SalesRepresentativeCity([
            'sales_representative_id' => $this->parent->id,
            'city_id' => $this->filhoBBBB->legalPerson->city->id
        ]);
        Phactory::SalesRepresentativeCity([
            'sales_representative_id' => $this->parent->id,
            'city_id' => $business->legalPerson->city->id
        ]);

        $configuracao = Configuration::find()
            ->where(['id' => Configuration::ID_CONVENIO_BUSINESS])
            ->one();
        $configuracao->value = $business->id + 100;
        $this->assertTrue($configuracao->save());

        $sale = new Sale;
        $sale->total = 100;
        $sale->invoice_code = '123abc';
        $sale->consumer_id = $this->filhoBBBB->id;
        $sale->business_id = $business->id;
        $sale->consumable_id = $consumable->id;

        $this->assertTrue($sale->save());
        $this->assertEquals(
            $business->legalPerson->city->attributes,
            $sale->getRepresentativeCity()->attributes
        );
    }

    public function testCalculateRepresentativeSaleProfit()
    {
        $this->generateTree();
        $consumable = Phactory::consumable(['shared_percentage' => 5]);
        $representative = Phactory::SalesRepresentativeCity([
            'sales_representative_id' => $this->parent->id,
            'city_id' => $this->filhoBBBB->legalPerson->city->id
        ]);

        $sale = new Sale;
        $sale->total = 100;
        $sale->invoice_code = '123abc';
        $sale->consumer_id = $this->filhoBBBB->id;
        $sale->business_id = Phactory::business()->id;
        $sale->consumable_id = $consumable->id;

        $this->assertTrue($sale->save());

        $this->assertEquals(0.25, $sale->calculateRepresentativeSaleProfit());
    }

    public function testSaleTreeParent()
    {
        $this->generateTree();

        $sale = new Sale;
        $sale->total = 100;
        $sale->invoice_code = '123abc';
        $sale->consumer_id = $this->parent->id;
        $sale->business_id = Phactory::business()->id;
        $sale->consumable_id = Phactory::consumable()->id;

        $this->assertTrue($sale->save());

        $this->assertEquals(1, TransactionDetail::find()->count());
    }

    public function testSaleValueTreeParent()
    {
        $this->generateTree();

        $consumable = Phactory::consumable(['shared_percentage' => 15]);

        $sale = new Sale;
        $sale->total = 1000.00;
        $sale->invoice_code = '123abc';
        $sale->consumer_id = $this->parent->id;
        $sale->business_id = $consumable->business_id;
        $sale->consumable_id = $consumable->id;

        $this->assertTrue($sale->save());

        $detail = TransactionDetail::find()->one();

        $this->assertEquals(11.00, $detail->profit_percentage);
        $this->assertEquals(TransactionDetail::TRANSACTION_ORIGIN_HIM, $detail->transaction_origin);

        $repasse = 1000 * 0.15;
        $pontos = $repasse * 0.6;

        $this->assertEquals(($pontos * 0.11), $detail->profit);
    }

    public function testSaleValueTreeChildren()
    {
        $this->generateTree();

        $consumable = Phactory::consumable(['shared_percentage' => 15]);

        $sale = new Sale;
        $sale->total = 1000.00;
        $sale->invoice_code = '123abc';
        $sale->consumer_id = $this->filhoBBBB->id;
        $sale->business_id = $consumable->business_id;
        $sale->consumable_id = $consumable->id;

        $this->assertTrue($sale->save());

        //filho
        $detail = TransactionDetail::find()->where(['consumer_id' => $this->filhoBBBB->id])->one();

        $this->assertEquals(11.00, $detail->profit_percentage);
        $this->assertEquals(TransactionDetail::TRANSACTION_ORIGIN_HIM, $detail->transaction_origin);

        $repasse = 1000 * 0.15;
        $pontos = $repasse * 0.6;

        $this->assertEquals(($pontos * 0.11), $detail->profit);
    }

    public function testSaleValueUnsharedFeesTreeParent()
    {
        $this->generateTree();

        $consumable = Phactory::consumable(['shared_percentage' => 15]);

        $sale = new Sale;
        $sale->total = 1000.00;
        $sale->invoice_code = '123abc';
        $sale->consumer_id = $this->parent->id;
        $sale->business_id = $consumable->business_id;
        $sale->consumable_id = $consumable->id;

        $this->assertTrue($sale->save());

        $repasse = 1000 * 0.15;
        $pontos = $repasse * 0.6;

        $sale->refresh();

        $this->assertEquals(($pontos - ($pontos * 0.11)), $sale->unshared_fees);
    }

    public function testSaleValueUnsharedFeesTreeChildren()
    {
        $this->generateTree();

        $consumable = Phactory::consumable(['shared_percentage' => 15]);

        $sale = new Sale;
        $sale->total = 1000.00;
        $sale->invoice_code = '123abc';
        $sale->consumer_id = $this->filhoBBBB->id;
        $sale->business_id = $consumable->business_id;
        $sale->consumable_id = $consumable->id;

        $this->assertTrue($sale->save());

        $repasse = 1000 * 0.15;
        $pontos = $repasse * 0.6;

        $sale->refresh();

        $this->assertEquals(($pontos - ($pontos * 0.11)), $sale->unshared_fees);
    }
    //valor nao computado na venda do filho = 100 - (11) = 89% (sale.unshared_fees)

    public function testRegisterToEntrepreuner()
    {
        $this->generateTree();

        $consumable = Phactory::consumable(['shared_percentage' => 15]);

        $sale = new Sale;
        $sale->total = 1000.00;
        $sale->invoice_code = '123abc';
        $sale->consumer_id = $this->filhoABAAA->id;
        $sale->business_id = $consumable->business_id;
        $sale->consumable_id = $consumable->id;

        $this->assertTrue($sale->save());
        $this->assertEquals(2, TransactionDetail::find()->count());
    }

     public function testSaleReportTotals()
    {
        Phactory::qualification(['completed_levels' => 1]);
        $parent = Phactory::consumer();
        $plane = Phactory::plane(['multiplier' => 0.6]);
        $filho = Phactory::consumer([
            'parentConsumer' => $parent,
            'sponsorConsumer' => $parent,
            'plane' => $plane
        ]);
        $consumable = Phactory::consumable(['shared_percentage' => 15]);

        $sale = Phactory::Sale([
            'total' => 1000,
            'consumer' => $filho,
            'consumable' => $consumable,
        ]);

        $sale2 = Phactory::Sale([
            'total' => 2000,
            'consumer' => $filho,
            'consumable' => $consumable,
        ]);

        $this->assertEquals(3000, SalesReport::getTotal());
    }

    public function testSaleReportTotalRepasse()
    {
        Phactory::qualification(['completed_levels' => 1]);
        $parent = Phactory::consumer();
        $plane = Phactory::plane(['multiplier' => 0.6]);
        $filho = Phactory::consumer([
            'parentConsumer' => $parent,
            'sponsorConsumer' => $parent,
            'plane' => $plane
        ]);
        $consumable = Phactory::consumable(['shared_percentage' => 15]);

        $sale = Phactory::Sale([
            'total' => 1000,
            'consumer' => $filho,
            'consumable' => $consumable,
        ]);

        $sale2 = Phactory::Sale([
            'total' => 2000,
            'consumer' => $filho,
            'consumable' => $consumable,
        ]);

        $this->assertEquals(450, SalesReport::getTotalFees());
    }

    public function testSaleReportTotalsWithConsumerFilter()
    {
        Phactory::qualification(['completed_levels' => 1]);
        $parent = Phactory::consumer();
        $business = Phactory::business();
        $business2 = Phactory::business();
        $plane = Phactory::plane(['multiplier' => 0.6]);
        $filho = Phactory::consumer([
            'parentConsumer' => $parent,
            'sponsorConsumer' => $parent,
            'plane' => $plane,
            'position' => 'right'
        ]);
        $consumable = Phactory::consumable([
            'shared_percentage' => 15,
            'business' => $business,
        ]);

        $user = Phactory::User([
            'authenticable_type' => 'Consumer',
            'authenticable_id' => $parent->id,
        ]);

        $sale = new Sale;
        $sale->total = 1000;
        $sale->invoice_code = '123abc';
        $sale->consumer_id = $filho->id;
        $sale->business_id = $consumable->business->id;
        $sale->consumable_id = $consumable->id;
        $this->assertTrue($sale->save());

        $sale2 = new Sale;
        $sale2->total = 2000;
        $sale2->invoice_code = '1234abcd';
        $sale2->consumer_id = $filho->id;
        $sale2->business_id = $consumable->business->id;
        $sale2->consumable_id = $consumable->id;
        $this->assertTrue($sale2->save());

        $sale3 = new Sale;
        $sale3->total = 3000;
        $sale3->invoice_code = '12345abcde';
        $sale3->consumer_id = $filho->id;
        $sale3->business_id = $business2->id;
        $sale3->consumable_id = $consumable->id;
        $this->assertTrue($sale3->save());

        $this->assertEquals(3000, SalesReport::getTotal(null, null, $consumable->business->id));
    }

    /**
     * @return void
     **/
    protected function generateTree()
    {
        $plane = Phactory::plane(['multiplier' => 0.6]);
        $planeOuro = Phactory::plane(['multiplier' => 0.62]);
        $planeBronze = Phactory::plane(['multiplier' => 0.52]);

        //Qualifições
        $consumidorQualification = Phactory::qualification([
            'description' => 'Consumidor',
            'gain_percentage' => 11,
            'position' => 11,
            'completed_levels' => 1,
            'register_network_sale' => false,
        ]);
        $empreendedorQualification = Phactory::qualification([
            'description' => 'Empreendedor',
            'gain_percentage' => 8,
            'position' => 10,
            'completed_levels' => 3,
            'register_network_sale' => true,
        ]);
        $treinadorQualification = Phactory::qualification([
            'description' => 'Treinador',
            'gain_percentage' => 8,
            'position' => 9,
            'completed_levels' => 4,
            'register_network_sale' => true,
        ]);
        $supervisorQualification = Phactory::qualification([
            'description' => 'Supervisor',
            'gain_percentage' => 8,
            'position' => 8,
            'completed_levels' => 6,
            'register_network_sale' => true,
        ]);

        $this->parent = $parent = Phactory::consumer([
            'plane' => $plane,
            'is_business_representative' => TRUE
        ]);

        //Familia A

        $filhoA = Phactory::consumer([
            'parentConsumer' => $parent,
            'sponsorConsumer' => $parent,
            'position' => 'left'
        ]);
        $filhoAA = Phactory::consumer([
            'parentConsumer' => $filhoA,
            'sponsorConsumer' => $parent,
            'plane' => $planeBronze,
            'position' => 'left'
            ]);
        $filhoAB = Phactory::consumer([
            'parentConsumer' => $filhoA,
            'sponsorConsumer' => $parent,
            'position' => 'right'
        ]);
        $filhoAAA = Phactory::consumer([
            'parentConsumer' => $filhoAA,
            'sponsorConsumer' => $parent,
            'position' => 'left'
        ]);
        $filhoAAB = Phactory::consumer([
            'parentConsumer' => $filhoAA,
            'sponsorConsumer' => $parent,
            'position' => 'right'
        ]);
        $filhoABA = Phactory::consumer([
            'parentConsumer' => $filhoAB,
            'sponsorConsumer' => $parent,
            'position' => 'left'
        ]);
        $filhoABB = Phactory::consumer([
            'parentConsumer' => $filhoAB,
            'sponsorConsumer' => $parent,
            'position' => 'right'
        ]);
        $this->filhoAAAA = $filhoAAAA = Phactory::consumer([
            'parentConsumer' => $filhoAAA,
            'sponsorConsumer' => $parent,
            'plane' => $planeOuro,
            'position' => 'left'
        ]);
        $filhoAAAB = Phactory::consumer([
            'parentConsumer' => $filhoAAA,
            'sponsorConsumer' => $parent,
            'position' => 'right'
        ]);
        $filhoAABA = Phactory::consumer([
            'parentConsumer' => $filhoAAB,
            'sponsorConsumer' => $parent,
            'position' => 'left'
        ]);
        $filhoAABB = Phactory::consumer([
            'parentConsumer' => $filhoAAB,
            'sponsorConsumer' => $parent,
            'position' => 'right'
        ]);
        $filhoABAA = Phactory::consumer([
            'parentConsumer' => $filhoABA,
            'sponsorConsumer' => $parent,
            'position' => 'left',
        ]);
        $filhoABAB = Phactory::consumer([
            'parentConsumer' => $filhoABA,
            'sponsorConsumer' => $parent,
            'position' => 'right'
        ]);
        $filhoABBA = Phactory::consumer([
            'parentConsumer' => $filhoABB,
            'sponsorConsumer' => $parent,
            'position' => 'left'
        ]);
        $filhoABBB = Phactory::consumer([
            'parentConsumer' => $filhoABB,
            'sponsorConsumer' => $parent,
            'position' => 'right'
        ]);

        //Familia B

        $filhoB = Phactory::consumer([
            'parentConsumer' => $parent,
            'sponsorConsumer' => $parent,
            'position' => 'right'
        ]);
        $filhoBA = Phactory::consumer([
            'parentConsumer' => $filhoB,
            'sponsorConsumer' => $parent,
            'position' => 'left'
        ]);
        $filhoBB = Phactory::consumer([
            'parentConsumer' => $filhoB,
            'sponsorConsumer' => $parent,
            'position' => 'right'
        ]);
        $filhoBAA = Phactory::consumer([
            'parentConsumer' => $filhoBA,
            'sponsorConsumer' => $parent,
            'position' => 'left'
        ]);
        $filhoBAB = Phactory::consumer([
            'parentConsumer' => $filhoBA,
            'sponsorConsumer' => $parent,
            'position' => 'right'
        ]);
        $filhoBBA = Phactory::consumer([
            'parentConsumer' => $filhoBB,
            'sponsorConsumer' => $parent,
            'position' => 'left'
        ]);
        $filhoBBB = Phactory::consumer([
            'parentConsumer' => $filhoBB,
            'sponsorConsumer' => $parent,
            'position' => 'right'
        ]);
        $filhoBAAA = Phactory::consumer([
            'parentConsumer' => $filhoBAA,
            'sponsorConsumer' => $parent,
            'position' => 'left'
        ]);
        $filhoBAAB = Phactory::consumer([
            'parentConsumer' => $filhoBAA,
            'sponsorConsumer' => $parent,
            'position' => 'right'
        ]);
        $filhoBABA = Phactory::consumer([
            'parentConsumer' => $filhoBAB,
            'sponsorConsumer' => $parent,
            'position' => 'left'
        ]);
        $filhoBABB = Phactory::consumer([
            'parentConsumer' => $filhoBAB,
            'sponsorConsumer' => $parent,
            'position' => 'right'
        ]);
        $filhoBBAA = Phactory::consumer([
            'parentConsumer' => $filhoBBA,
            'sponsorConsumer' => $parent,
            'position' => 'left'
        ]);
        /*$filhoBBAB = Phactory::consumer([
            'parentConsumer' => $filhoBBA,
            'sponsorConsumer' => $parent,
            'position' => 'right'

        ]);*/
        $filhoBBBA = Phactory::consumer([
            'parentConsumer' => $filhoBBB,
            'sponsorConsumer' => $parent,
            'position' => 'left'
        ]);
        $this->filhoBBBB = $filhoBBBB = Phactory::consumer([
            'parentConsumer' => $filhoBBB,
            'sponsorConsumer' => $parent,
            'position' => 'right',
            'plane' => $plane
        ]);

        //Another Test

        $this->filhoABAAA = $filhoABAAA = Phactory::consumer([
            'parentConsumer' => $filhoABAA,
            'sponsorConsumer' => $parent,
            'position' => 'left',
            'plane' => $plane
        ]);
        $filhoABAAB = Phactory::consumer([
            'parentConsumer' => $filhoABAA,
            'sponsorConsumer' => $parent,
            'position' => 'right',
            'plane' => $plane
        ]);
        $filhoABABA = Phactory::consumer([
            'parentConsumer' => $filhoABAB,
            'sponsorConsumer' => $parent,
            'position' => 'left',
            'plane' => $plane
        ]);
        $filhoABABB = Phactory::consumer([
            'parentConsumer' => $filhoABAB,
            'sponsorConsumer' => $parent,
            'position' => 'right',
            'plane' => $plane
        ]);
        $filhoABBAA = Phactory::consumer([
            'parentConsumer' => $filhoABBA,
            'sponsorConsumer' => $parent,
            'position' => 'left',
            'plane' => $plane
        ]);
        $filhoABBAB = Phactory::consumer([
            'parentConsumer' => $filhoABBA,
            'sponsorConsumer' => $parent,
            'position' => 'right',
            'plane' => $plane
        ]);
        $filhoABBBA = Phactory::consumer([
            'parentConsumer' => $filhoABBB,
            'sponsorConsumer' => $parent,
            'position' => 'left',
            'plane' => $plane
        ]);
        $filhoABBBB = Phactory::consumer([
            'parentConsumer' => $filhoABBB,
            'sponsorConsumer' => $parent,
            'position' => 'right',
            'plane' => $plane
        ]);
    }
}
