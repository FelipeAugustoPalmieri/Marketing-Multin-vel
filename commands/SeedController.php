<?php
namespace app\commands;

use app\models\Consumer;
use Yii;
use yii\console\Controller;
use yii\helpers\Console;
use Phactory;
use perspectiva\phactory\DbCleaner;
use tests\FakerTrait;

require_once __DIR__ . '/../tests/FakerTrait.php';

class SeedController extends Controller
{
    use FakerTrait;

    const BUSINESSES_AMOUNT = 11;
    const CONSUMERS_AMOUNT = 50;
    const UNACTIVATED_CONSUMERS_AMOUNT = 5;
    const EXPIRED_CONSUMERS_AMOUNT = 3;

    public function actionIndex()
    {
        $this->stderr(
            "WARNING! This command will DROP THE DATABASE and recreate it with fake data.\n",
            Console::FG_RED
        );

        if ($this->prompt('Are you sure you want to proceed? (YES / NO)') != 'YES') {
            return $this->stdout("Command cancelled.\n");
        }

        Yii::setAlias('@tests', dirname(__DIR__) . '/tests');
        $this->stdout("Recreating database...\n");
        DbCleaner::recreate();
        $this->stdout("Creating fake data...\n");

        // Master user
        $this->stdout('Users');
        $user = Phactory::user([
            'name' => 'Admin',
            'login' => 'admin',
            'encrypted_password' => password_hash('admin', PASSWORD_BCRYPT),
            'authenticable_type' => null,
            'authenticable_id' => null,
        ]);
        Yii::$app->authManager->assign(
            Yii::$app->authManager->getRole('admin'),
            $user->getId()
        );
        $this->stdout(" OK\n", Console::FG_GREEN);

        // Businesses
        $legalPersonTypes = ['physicalPerson', 'juridicalPerson'];
        $this->executeNTimes(
            self::BUSINESSES_AMOUNT,
            'Businesses',
            function() use ($legalPersonTypes) {
                Phactory::business(
                    $legalPersonTypes[rand(0, 1)]
                );
            }
        );

        // Consumers
        Phactory::consumer(['paid_affiliation_fee' => true]); // raiz da árvore
        $this->executeNTimes(
            self::CONSUMERS_AMOUNT,
            'Consumers',
            function() {
                $parent = Consumer::find()
                    ->ableToHaveChildren()
                    ->orderBy('RANDOM()')
                    ->one()
                ;

                $position = array_rand(['left', 'right']);

                if ($parent->getChildrenConsumers()->count() > 0) {
                        $position = 'left';
                    } else {
                        $position = 'right';
                }

                $consumer = Phactory::consumer([
                    'paid_affiliation_fee' => true,
                    'parentConsumer' => $parent,
                    'parent_consumer_id' => $parent->id,
                    'sponsor_consumer_id' => $parent->id,
                    'position' => $position,
                ]);

                if ($consumer->paid_affiliation_fee) {
                    Phactory::user([
                        'name' => $consumer->legalPerson->name,
                        'email' => $consumer->legalPerson->email,
                        'login' => $consumer->legalPerson->email,
                        'authenticable_id' => $consumer->id,
                        'authenticable_type' => 'Consumer',
                    ]);
                }
            }
        );

        // Unactivated consumers
        $this->executeNTimes(
            self::UNACTIVATED_CONSUMERS_AMOUNT,
            'Consumers (unactivated)',
            function() {
                $parent = Consumer::find()
                    ->ableToHaveChildren()
                    ->orderBy('RANDOM()')
                    ->one()
                ;

                $position = array_rand(['left', 'right']);

                if ($parent->getChildrenConsumers()->count() > 0) {
                        $position = 'left';
                    } else {
                        $position = 'right';
                }

                $consumer = Phactory::consumer([
                    'paid_affiliation_fee' => false,
                    'parentConsumer' => $parent,
                    'parent_consumer_id' => $parent->id,
                    'sponsor_consumer_id' => $parent->id,
                    'position' => $position,
                ]);
            }
        );

        // Expired consumers
        $this->executeNTimes(
            self::EXPIRED_CONSUMERS_AMOUNT,
            'Consumers (expired)',
            function() {
                $parent = Consumer::find()
                    ->ableToHaveChildren()
                    ->orderBy('RANDOM()')
                    ->one()
                ;

                $position = array_rand(['left', 'right']);

                if ($parent->getChildrenConsumers()->count() > 0) {
                        $position = 'left';
                    } else {
                        $position = 'right';
                }

                $consumer = Phactory::consumer([
                    'paid_affiliation_fee' => false,
                    'parentConsumer' => $parent,
                    'parent_consumer_id' => $parent->id,
                    'sponsor_consumer_id' => $parent->id,
                    'position' => $position,
                ]);
                $consumer = Consumer::findOne($consumer->id);
                $consumer->created_at = date('Y-m-d H:i:s', strtotime('today - ' . round(6, 9) . ' days'));
                $consumer->update(['created_at']);
            }
        );
    }

    protected function executeNTimes($n, $mensagem, $funcao)
    {
        $this->stdout($mensagem);
        for ($i = 0; $i < $n; $i++) {
            $funcao();
        }
        $this->stdout(" OK\n", Console::FG_GREEN);
    }
}
