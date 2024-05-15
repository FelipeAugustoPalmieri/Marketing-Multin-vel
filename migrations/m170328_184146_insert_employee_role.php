<?php

use yii\db\Migration;

class m170328_184146_insert_employee_role extends Migration
{
    public function up()
    {
        $auth = Yii::$app->authManager;

        $roles = [
            'employee' => 'Employee',
        ];

        foreach ($roles as $name => $description) {
            $role = $auth->createRole($name);
            $role->description = $description;
            $auth->add($role);
        }

    }

    public function down()
    {
        $auth->removeItem($auth->getRole('employee'));
    }
}
