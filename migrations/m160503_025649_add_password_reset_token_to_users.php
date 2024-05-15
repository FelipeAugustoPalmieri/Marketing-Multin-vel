<?php

use yii\db\Migration;

class m160503_025649_add_password_reset_token_to_users extends Migration
{
    public function up()
    {
        $this->addColumn('users', 'reset_password_token', $this->string());
    }

    public function down()
    {
        $this->dropColumn('users', 'reset_password_token');
    }
}
