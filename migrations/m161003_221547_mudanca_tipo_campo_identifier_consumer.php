<?php

use yii\db\Migration;

class m161003_221547_mudanca_tipo_campo_identifier_consumer extends Migration
{
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
        $this->renameColumn('consumers', 'identifier', 'identifier_old');
        $this->addColumn('consumers', 'identifier', $this->integer());

        $this->execute('update consumers set identifier = cast(identifier_old as integer)');

        $this->createIndex('idx_consumers_identifier', 'consumers', ['identifier']);

        $this->dropColumn('consumers', 'identifier_old');
    }

    public function safeDown()
    {
        $this->dropIndex('idx_consumers_identifier', 'consumers');
        $this->dropColumn('consumers', 'identifier');
        $this->renameColumn('consumers', 'identifier_old', 'identifier');
    }
}
