<?php
namespace tests\unit\models;

use app\models\Consumer;
use app\models\SalesRepresentativeCity;
use app\models\TransactionDetail;
use app\models\query\ConsumerQuery;
use Phactory;
use perspectiva\phactory\ActiveRecordTest;
use tests\FakerTrait;
use yii\db\ActiveQuery;

class ConsumerTest extends ActiveRecordTest
{
    use FakerTrait;

    public function testTimestampBehavior()
    {
        $model = Phactory::consumer();
        $this->assertNotNull($model->created_at);
        $this->assertNotNull($model->updated_at);
    }

    public function testFindReturnsConsumerQueryInsteadOfActiveQuery()
    {
        $this->assertInstanceOf(ConsumerQuery::className(), Consumer::find());
    }

    public function testGetChildrenConsumers()
    {
        $parent = Phactory::consumer();
        $filho = Phactory::consumer(['parentConsumer' => $parent, 'sponsorConsumer' => $parent]);

        $this->assertInstanceOf(ActiveQuery::className(), $parent->getChildrenConsumers());
        $this->assertInternalType('array', $parent->childrenConsumers);
        $this->assertInstanceOf(Consumer::className(), $parent->childrenConsumers[0]);
        $this->assertEquals($filho->id, $parent->childrenConsumers[0]->id);
    }

    public function testGetParentConsumer()
    {
        $parent = Phactory::consumer();
        $filho = Phactory::consumer(['parentConsumer' => $parent, 'sponsorConsumer' => $parent]);
        $query = $filho->getParentConsumer();

        $this->assertInstanceOf(ActiveQuery::className(), $query);
        $this->assertInstanceOf(Consumer::className(), $query->one());
        $this->assertEquals($parent->id, $query->one()->id);
    }

    public function testGetSponsorConsumer()
    {
        $sponsor = Phactory::consumer();
        $sponsored = Phactory::consumer(['parentConsumer' => $sponsor, 'sponsorConsumer' => $sponsor]);
        $query = $sponsored->getSponsorConsumer();

        $this->assertInstanceOf(ActiveQuery::className(), $query);
        $this->assertInstanceOf(Consumer::className(), $query->one());
        $this->assertEquals($sponsor->id, $query->one()->id);
    }

    public function testDoesntAllowTwoRootConsumers()
    {
        $raiz1 = Phactory::consumer();
        $raiz2 = Phactory::consumer(['parentConsumer' => $raiz1, 'sponsorConsumer' => $raiz1]);
        $raiz2->parent_consumer_id = null;
        $raiz3 = Phactory::unsavedConsumer([
            'legal_person_id' => Phactory::legalPerson('physicalPerson')->id,
        ]);

        $this->assertTrue($raiz1->save(), 'Deveria permitir editar um consumer que já era raiz');
        $this->assertFalse($raiz2->save(), 'Deveria bloquear edição criando nova raiz');
        $this->assertFalse($raiz3->save(), 'Deveria bloquear o cadastro de nova raiz');
    }

    public function testDoesntAllowLoopBetweenParentAndChildren()
    {
        $raiz = Phactory::consumer();
        $nivel1 = Phactory::consumer(['parentConsumer' => $raiz, 'sponsorConsumer' => $raiz]);
        $nivel2 = Phactory::consumer(['parentConsumer' => $nivel1, 'sponsorConsumer' => $raiz]);
        $nivel3 = Phactory::consumer(['parentConsumer' => $nivel2, 'sponsorConsumer' => $raiz]);

        $nivel1->parent_consumer_id = $nivel3->id;
        $this->assertFalse($nivel1->save(), 'Deveria bloquear definir o pai de um consumer com um de seus descendentes');
    }

    public function testDoesntAllowMoreChildrenThanTheLimit()
    {
        $parent = Phactory::consumer();
        $filho1 = Phactory::consumer(['parentConsumer' => $parent, 'sponsorConsumer' => $parent]);
        $filho2 = Phactory::consumer(['parentConsumer' => $parent, 'sponsorConsumer' => $parent, 'position' => 'right']);
        $filho3 = Phactory::unsavedConsumer([
            'legal_person_id' => Phactory::legalPerson('physicalPerson')->id,
            'sponsor_consumer_id' => $parent->id,
        ]);

        $filho3->parent_consumer_id = $parent->id;
        $this->assertFalse($filho3->save(), 'Não pode deixar que um pai tenha mais de 2 filhos');

        $filho3->parent_consumer_id = $filho1->id;
        $this->assertTrue($filho3->save());
    }

    public function testActive()
    {
        $raiz = Phactory::consumer();
        $nivel1 = Phactory::consumer(['parentConsumer' => $raiz, 'sponsorConsumer' => $raiz]);
        $nivel2 = Phactory::consumer([
            'paid_affiliation_fee' => false,
            'parentConsumer' => $nivel1,
            'sponsorConsumer' => $raiz,
        ]);

        $this->assertTrue($nivel2->activate());
    }

    public function testActiveSaveDetails()
    {
        $raiz = Phactory::consumer();
        $nivel1 = Phactory::consumer(['parentConsumer' => $raiz, 'sponsorConsumer' => $raiz]);
        $nivel2 = Phactory::consumer([
            'parentConsumer' => $nivel1,
            'sponsorConsumer' => $raiz,
            'paid_affiliation_fee' => FALSE

        ]);

        $this->assertTrue($nivel2->activate());

        $this->assertEquals(1, TransactionDetail::find()->count());
    }

    public function testActiveSaveDetailsWithRepresentativeCity()
    {
        $raiz = Phactory::consumer(['is_business_representative' => TRUE]);
        $nivel1 = Phactory::consumer(['parentConsumer' => $raiz, 'sponsorConsumer' => $raiz]);
        $nivel2 = Phactory::consumer([
            'parentConsumer' => $nivel1,
            'sponsorConsumer' => $raiz,
            'paid_affiliation_fee' => FALSE

        ]);

        $representative = Phactory::SalesRepresentativeCity([
            'sales_representative_id' => $raiz->id,
            'city_id' => $nivel2->legalPerson->city->id
        ]);

        $this->assertTrue($nivel2->activate());

        $this->assertEquals(2, TransactionDetail::find()->count());
    }

    public function testCalculateRepresentativeActivateProfit()
    {
        $raiz = Phactory::consumer(['is_business_representative' => TRUE]);
        $plane = Phactory::plane(['value' => 600]);
        $filho = Phactory::consumer([
            'parentConsumer' => $raiz,
            'sponsorConsumer' => $raiz,
            'plane' => $plane,
            'paid_affiliation_fee' => FALSE
        ]);

        $representative = Phactory::SalesRepresentativeCity([
            'sales_representative_id' => $raiz->id,
            'city_id' => $filho->legalPerson->city->id
        ]);

        $this->assertTrue($filho->activate());

        $this->assertEquals(30, $filho->calculateRepresentativeActivateProfit());

    }
    public function testQualificationConsumerToEntrepreneur()
    {
        Phactory::qualification([
            'description' => 'Empreendedor',
            'gain_percentage' => 8,
            'position' => 10,
            'completed_levels' => 3,
            'register_network_sale' => true,
        ]);

        $parent = Phactory::consumer();
        $plane = Phactory::plane(['multiplier' => 0.6]);
        $filho = Phactory::consumer([
            'parentConsumer' => $parent,
            'sponsorConsumer' => $parent,
            'plane' => $plane,
            'position' => 'left'
        ]);
        $filho2 = Phactory::consumer([
            'parentConsumer' => $parent,
            'sponsorConsumer' => $parent,
            'plane' => $plane,
            'position' => 'right'
        ]);
        $filho3 = Phactory::consumer([
            'parentConsumer' => $filho,
            'sponsorConsumer' => $filho,
            'plane' => $plane,
            'position' => 'left'
        ]);
        $filho4 = Phactory::consumer([
            'parentConsumer' => $filho,
            'sponsorConsumer' => $filho,
            'plane' => $plane,
            'position' => 'right'
        ]);
        $filho5 = Phactory::consumer([
            'parentConsumer' => $filho2,
            'sponsorConsumer' => $filho2,
            'plane' => $plane,
            'position' => 'left'
        ]);
        $filho6 = Phactory::consumer([
            'parentConsumer' => $filho2,
            'sponsorConsumer' => $filho2,
            'plane' => $plane,
            'position' => 'right'
        ]);

        $this->assertEquals(3, $parent->getCompletedTreeLevels());
    }

    public function testMissingConsumers()
    {
        $qualification = Phactory::qualification([
            'description' => 'Empreendedor',
            'gain_percentage' => 8,
            'position' => 10,
            'completed_levels' => 3,
            'register_network_sale' => true,
        ]);

        $parent = Phactory::consumer();
        $plane = Phactory::plane(['multiplier' => 0.6]);
        $filho = Phactory::consumer([
            'parentConsumer' => $parent,
            'sponsorConsumer' => $parent,
            'plane' => $plane,
            'position' => 'left'
        ]);
        $filho2 = Phactory::consumer([
            'parentConsumer' => $parent,
            'sponsorConsumer' => $parent,
            'plane' => $plane,
            'position' => 'right'
        ]);
        $filho3 = Phactory::consumer([
            'parentConsumer' => $filho,
            'sponsorConsumer' => $filho,
            'plane' => $plane,
            'position' => 'left'
        ]);
        $filho4 = Phactory::consumer([
            'parentConsumer' => $filho,
            'sponsorConsumer' => $filho,
            'plane' => $plane,
            'position' => 'right'
        ]);
        $this->assertEquals(2, $parent->getMissingConsumers($qualification));
    }

    public function testCompletedLevels()
    {
        $qualification = Phactory::qualification([
            'description' => 'Empreendedor',
            'gain_percentage' => 8,
            'position' => 10,
            'completed_levels' => 3,
            'register_network_sale' => true,
        ]);

        $parent = Phactory::consumer();
        $plane = Phactory::plane(['multiplier' => 0.6]);
        $filho = Phactory::consumer([
            'parentConsumer' => $parent,
            'sponsorConsumer' => $parent,
            'plane' => $plane,
            'position' => 'left'
        ]);
        $filho2 = Phactory::consumer([
            'parentConsumer' => $parent,
            'sponsorConsumer' => $parent,
            'plane' => $plane,
            'position' => 'right'
        ]);
        $filho3 = Phactory::consumer([
            'parentConsumer' => $filho,
            'sponsorConsumer' => $filho,
            'plane' => $plane,
            'position' => 'left'
        ]);
        $filho4 = Phactory::consumer([
            'parentConsumer' => $filho,
            'sponsorConsumer' => $filho,
            'plane' => $plane,
            'position' => 'right'
        ]);
        $filho5 = Phactory::consumer([
            'parentConsumer' => $filho2,
            'sponsorConsumer' => $filho2,
            'plane' => $plane,
            'position' => 'left'
        ]);
        $filho6 = Phactory::consumer([
            'parentConsumer' => $filho2,
            'sponsorConsumer' => $filho2,
            'plane' => $plane,
            'position' => 'right'
        ]);

        $this->assertEquals(0, $parent->getMissingConsumers($qualification));
        $this->assertEquals(3, $parent->getCompletedTreeLevels());
    }

    public function testLastCompletedLevel()
    {
        $father = Phactory::consumer();
        $this->treeGenerator(8, $father);
        $this->assertEquals(8, $father->getCompletedTreeLevels());
        //está setado como 8 pois o tempo de execução é mais curto, caso queira testar um outro nivel apenas mudar mudar o número de niveis e o número esperado
    }

    protected function treeGenerator($levels, $father)
    {
        $completedLevels = 1;
        if ($completedLevels >= $levels) {
            return true;
        }

        $mailA = $father->id . 'testeA'. $levels .'@tbest.com';
        $mailB = $father->id . 'testeB'. $levels .'@tbest.com';

        $legalPersonA = Phactory::legalPerson([
            'person_class' => 'PhysicalPerson',
            'email' => $mailA
        ]);
        $legalPersonB = Phactory::legalPerson([
            'person_class' => 'PhysicalPerson',
            'email' => $mailB
        ]);

        $completedLevels++;
        $childrenA = Phactory::consumer([
            'parentConsumer' => $father,
            'sponsorConsumer' => $father,
            'legalPerson' => $legalPersonA,
            'position' => 'left'
        ]);
        $childrenB = Phactory::consumer([
            'parentConsumer' => $father,
            'sponsorConsumer' => $father,
            'legalPerson' => $legalPersonB,
            'position' => 'right'
        ]);
        $this->treeGenerator($levels - 1, $childrenA);
        $this->treeGenerator($levels - 1, $childrenB);


        return true;
    }
}
