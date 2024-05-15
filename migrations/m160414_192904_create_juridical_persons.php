<?php

use yii\db\Migration;

class m160414_192904_create_juridical_persons extends Migration
{
    public function safeUp()
    {
        $this->createTable(
            'juridical_persons',
            [
                'id' => $this->primaryKey(),
                'created_at' => $this->timestamp()->notNull(),
                'updated_at' => $this->timestamp(),
                'company_name' => $this->string()->notNull(),
                'trading_name' => $this->string()->notNull(),
                'contact_name' => $this->string()->notNull(),
                'cnpj' => $this->string()->notNull()->unique(),
                'ie' => $this->string(),
                'website' => $this->string(),
                'economic_activity' => $this->string(),
                'shared_percentage' => $this->float(),
            ]
        );
    }

    public function safeDown()
    {
        $this->dropTable('juridical_persons');
    }
}
