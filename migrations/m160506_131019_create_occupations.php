<?php

use yii\db\Migration;

/**
 * Handles the creation for table `occupations`.
 */
class m160506_131019_create_occupations extends Migration
{
    public function safeUp()
    {
        $this->createTable(
            'occupations',
            [
                'id' => $this->primaryKey(),
                'created_at' => $this->timestamp()->notNull(),
                'updated_at' => $this->timestamp(),
                'name' => $this->string()->notNull(),
            ]
        );

        $newId = 1;
        if (($handle = fopen($this->getCsvFilePath(), 'r')) !== false) {
            $rows = [];
            while (($row = fgetcsv($handle, 0, ';')) !== false) {
                if (!is_numeric($row[1])) {
                    continue;
                }
                $rows[] = [
                    'id' => $row[1],
                    'created_at' => date('Y-m-d H:i:s'),
                    'name' => $row[0],
                ];
                $newId = $row[1] + 1;
            }
            $this->batchInsert(
                'occupations',
                ['id', 'created_at', 'name'],
                $rows
            );
            fclose($handle);
        }

        $this->db->createCommand()->resetSequence('occupations', $newId)->execute();
    }

    public function safeDown()
    {
        $this->dropTable('occupations');
    }

    protected function getCsvFilePath()
    {
        if (YII_ENV == 'test') {
            return dirname(__DIR__) . '/tests/data/br-occupations.csv';
        }
        return dirname(__DIR__) . '/data/br-occupations.csv';
    }
}
