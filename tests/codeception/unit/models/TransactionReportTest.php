<?php
namespace tests\unit\models;

use Phactory;
use app\models\Sale;
use app\models\TransactionDetail;
use app\models\TransactionReport;
use perspectiva\phactory\Test;
use yii\db\ActiveQuery;
use tests\FakerTrait;
use Yii;

class TransactionReportTest extends Test
{
    use FakerTrait;

    public $userA;
    public $userB;

    public function testFiltroData()
    {
        $this->getReportScenario();

        $this->assertEquals(3, TransactionDetail::find()->count());

        $form = new TransactionReport;
        $form->user = $this->userA;
        $form->period = date('Y-m');

        $this->assertEquals(2, $form->getTransactionReport()->getCount());
    }

    public function testFiltroNaoAchaDataSemRegistro()
    {
        $this->getReportScenario();

        $this->assertEquals(3, TransactionDetail::find()->count());

        $form = new TransactionReport;
        $form->user = $this->userA;
        $form->period = '2015-01';

        $this->assertEquals(0, $form->getTransactionReport()->getCount());
    }

    public function testHerarquiaRedeFilho()
    {
        $this->getReportScenario();

        $form = new TransactionReport;
        $form->user = $this->userB;

        $this->assertEquals(1, $form->getTransactionReport()->getCount());
    }

    public function testHorario()
    {
        $parent = Phactory::consumer();

        $sale = new Sale;
        $sale->consumer_id = $parent->id;
        $sale->business_id = Phactory::business()->id;
        $sale->consumable_id = Phactory::consumable()->id;
        $sale->invoice_code = '123456';
        $sale->total = 150;
        $this->assertTrue($sale->save());

        $transactionDetail = new TransactionDetail;
        $transactionDetail->object_type = 'Sale';
        $transactionDetail->object_id = $sale->id;
        $transactionDetail->consumer_id = $sale->consumer->id;
        $transactionDetail->plane_id = $sale->consumer->plane->id;
        $transactionDetail->profit_percentage = 10;
        $transactionDetail->profit = 40;
        $transactionDetail->created_at = date('Y-m-d H:i:s');
        $transactionDetail->transaction_origin = TransactionDetail::TRANSACTION_ORIGIN_HIM;
        $this->assertTrue($transactionDetail->save());
        $transactionDetail->created_at = date('2016-10-31 23:59:00');
        $this->assertTrue($transactionDetail->save());

        $user = Phactory::User([
            'authenticable_type' => 'Consumer',
            'authenticable_id' => $parent->id,
        ]);

        $form = new TransactionReport;
        $form->user = $user;
        $form->period = '2016-10';

        $this->assertEquals(1, $form->getTransactionReport()->getCount());
    }

    public function getReportScenario()
    {
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
        $filho = Phactory::consumer(['plane_id' => $plane->id, 'parentConsumer' => $parent, 'sponsorConsumer' => $parent]);

        $this->userA = Phactory::User([
            'authenticable_type' => 'Consumer',
            'authenticable_id' => $parent->id,
        ]);

        $this->userB = Phactory::User([
            'authenticable_type' => 'Consumer',
            'authenticable_id' => $filho->id,
        ]);

        //venda 1
        $sale = new Sale;
        $sale->consumer_id = $parent->id;
        $sale->business_id = Phactory::business()->id;
        $sale->consumable_id = Phactory::consumable()->id;
        $sale->invoice_code = '123456';
        $sale->total = 150;
        $this->assertTrue($sale->save());

        //venda 2
        $sale = new Sale;
        $sale->consumer_id = $parent->id;
        $sale->business_id = Phactory::business()->id;
        $sale->consumable_id = Phactory::consumable()->id;
        $sale->invoice_code = '123456';
        $sale->total = 150;
        $this->assertTrue($sale->save());

        //venda 3
        $sale = new Sale;
        $sale->consumer_id = $filho->id;
        $sale->business_id = Phactory::business()->id;
        $sale->consumable_id = Phactory::consumable()->id;
        $sale->invoice_code = '123456';
        $sale->total = 150;
        $this->assertTrue($sale->save());

        return;
    }
}
