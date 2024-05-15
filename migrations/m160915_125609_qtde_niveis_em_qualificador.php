<?php

use yii\db\Migration;

class m160915_125609_qtde_niveis_em_qualificador extends Migration
{
    public function up()
    {
        $this->addColumn('qualifications', 'completed_levels', $this->integer());
    }

    public function down()
    {
        $this->dropColumn('qualifications', 'completed_levels');
    }
}
