<?php

use yii\db\Migration;

class m161104_175515_add_column_points_to_qualification extends Migration
{
    public function up()
    {
        $this->addColumn('qualifications', 'points', $this->float());
    }

    public function down()
    {
        $this->dropColumn('qualifications', 'points');
    }
}
