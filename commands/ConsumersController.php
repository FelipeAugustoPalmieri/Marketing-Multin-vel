<?php
namespace app\commands;

use app\models\Consumer;
use Yii;
use yii\console\Controller;
use yii\helpers\Console;

class ConsumersController extends Controller
{
    public function actionDiscardUnactivated()
    {
        $this->stdout("Searching unactivated users... ");

        $date = date('Y-m-d 00:00:00', strtotime('now - 5 days'));
        $query = Consumer::find()
            ->unpaidAffiliation()
            ->createdBefore($date)
            ->with('legalPerson')
        ;

        $this->stdout($query->count() . " FOUND\n", Console::FG_GREEN);

        foreach ($query->all() as $consumer) {
            $transaction = Yii::$app->db->beginTransaction();
            $legalPerson = $consumer->legalPerson;
            $person = $legalPerson->person;
            $name = $consumer->legalPerson->name;
            $this->stdout("Removing {$name}... ");
            if ($consumer->delete() && $legalPerson->delete() && $person->delete()) {
                $transaction->commit();
                $this->stdout(" OK\n", Console::FG_GREEN);
            } else {
                $transaction->rollBack();
                $this->stdout(" ERROR\n", Console::FG_RED);
            }
        }

        $this->stdout("Done.\n", Console::FG_GREEN);
    }
}
