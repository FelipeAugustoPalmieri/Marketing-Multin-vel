<?php

use yii\db\Migration;

class m160417_131701_create_consumers extends Migration
{
    public function safeUp()
    {
        $this->createTable(
            'consumers',
            [
                'id' => $this->primaryKey(),
                'created_at' => $this->timestamp()->notNull(),
                'updated_at' => $this->timestamp(),
                'legal_person_id' => $this->integer()->notNull()->unique(),
                'parent_consumer_id' => $this->integer(),

                'identifier' => $this->string()->notNull()->unique(),
                'bank_name' => $this->string(),
                'bank_agency' => $this->string(),
                'bank_account' => $this->string(),

                'is_business_representative' => $this->boolean()->notNull()->defaultValue(false),
                'paid_affiliation_fee' => $this->boolean()->notNull()->defaultValue(false),
            ]
        );
        $this->addForeignKey(
            'fk-consumers-legal_person_id',
            'consumers',
            'legal_person_id',
            'legal_persons',
            'id'
        );
        $this->addForeignKey(
            'fk-consumers-parent_consumer_id',
            'consumers',
            'parent_consumer_id',
            'consumers',
            'id'
        );

        $this->addColumn('physical_persons', 'rg', $this->string());
        $this->addColumn('physical_persons', 'nationality', $this->string());
        $this->addColumn('physical_persons', 'occupation', $this->string());
        $this->addColumn('physical_persons', 'born_on', $this->date());
        $this->addColumn('physical_persons', 'marital_status', $this->string());
        $this->addColumn('physical_persons', 'partner_name', $this->string());
        $this->addColumn('physical_persons', 'partner_born_on', $this->date());
        $this->addColumn('physical_persons', 'partner_phone_number', $this->string());
        $this->addColumn('physical_persons', 'partner_cpf', $this->string());
        $this->addColumn('physical_persons', 'partner_rg', $this->string());
    }

    public function safeDown()
    {
        $this->dropColumn('physical_persons', 'rg');
        $this->dropColumn('physical_persons', 'nationality');
        $this->dropColumn('physical_persons', 'occupation');
        $this->dropColumn('physical_persons', 'born_on');
        $this->dropColumn('physical_persons', 'marital_status');
        $this->dropColumn('physical_persons', 'partner_name');
        $this->dropColumn('physical_persons', 'partner_born_on');
        $this->dropColumn('physical_persons', 'partner_phone_number');
        $this->dropColumn('physical_persons', 'partner_cpf');
        $this->dropColumn('physical_persons', 'partner_rg');

        $this->dropTable('consumers');
    }
}
