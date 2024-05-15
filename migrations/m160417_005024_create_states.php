<?php

use yii\db\Migration;

class m160417_005024_create_states extends Migration
{
    public function safeUp()
    {
        $this->createTable(
            'states',
            [
                'id' => $this->primaryKey(),
                'created_at' => $this->timestamp()->notNull(),
                'updated_at' => $this->timestamp(),
                'name' => $this->string()->notNull(),
                'abbreviation' => $this->string(2)->notNull(),
            ]
        );

        $newId = 1;
        if (($handle = fopen($this->getCsvFilePath(), 'r')) !== false) {
            $rows = [];
            while (($row = fgetcsv($handle, 0, ';')) !== false) {
                if (!is_numeric($row[0])) {
                    continue;
                }
                $rows[] = [
                    'id' => $row[0],
                    'created_at' => date('Y-m-d H:i:s'),
                    'name' => $row[1],
                    'abbreviation' => $row[2],
                ];
                $newId = $row[0] + 1;
            }
            $this->batchInsert(
                'states',
                ['id', 'created_at', 'name', 'abbreviation'],
                $rows
            );
            fclose ($handle);
        }

        $this->db->createCommand()->resetSequence('states', $newId)->execute();
    }

    public function down()
    {
        $this->dropTable('states');
    }

    protected function getCsvFilePath()
    {
        if (YII_ENV == 'test') {
            return dirname(__DIR__) . '/tests/data/br-states.csv';
        }
        return dirname(__DIR__) . '/data/br-states.csv';
    }
}
