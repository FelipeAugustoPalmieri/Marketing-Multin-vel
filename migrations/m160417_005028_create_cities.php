<?php

use yii\db\Migration;

class m160417_005028_create_cities extends Migration
{
    public function safeUp()
    {
        $this->createTable(
            'cities',
            [
                'id' => $this->primaryKey(),
                'created_at' => $this->timestamp()->notNull(),
                'updated_at' => $this->timestamp(),
                'name' => $this->string()->notNull(),
                'state_id' => $this->integer()->notNull(),
            ]
        );
        $this->addForeignKey(
            'fk-cities-state_id',
            'cities',
            'state_id',
            'states',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $newId = 1;
        if (($handle = fopen($this->getCsvFilePath(), 'r')) !== false) {
            $rows = [];
            while (($row = fgetcsv($handle, 0, ';')) !== false) {
                if (!is_numeric($row[0])) {
                    continue;
                }
                $rows[] = [
                    'id' => $row[1],
                    'created_at' => date('Y-m-d H:i:s'),
                    'state_id' => $row[0],
                    'name' => $row[2],
                ];
                $newId = $row[1] + 1;
            }
            $this->batchInsert(
                'cities',
                ['id', 'created_at', 'state_id', 'name'],
                $rows
            );
            fclose($handle);
        }

        $this->db->createCommand()->resetSequence('cities', $newId)->execute();
    }

    public function safeDown()
    {
        $this->dropTable('cities');
    }

    protected function getCsvFilePath()
    {
        if (YII_ENV == 'test') {
            return dirname(__DIR__) . '/tests/data/br-cities.csv';
        }
        return dirname(__DIR__) . '/data/br-cities.csv';
    }
}
