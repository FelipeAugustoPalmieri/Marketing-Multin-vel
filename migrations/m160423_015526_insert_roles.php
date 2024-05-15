<?php

use yii\db\Migration;

class m160423_015526_insert_roles extends Migration
{
    public function up()
    {
        $auth = Yii::$app->authManager;

        // Roles
        $roles = [
            'admin' => 'Admin',
            'business' => 'Business',
            'consumer' => 'Consumer',
        ];

        foreach ($roles as $name => $description) {
            $role = $auth->createRole($name);
            $role->description = $description;
            $auth->add($role);
        }

        // Permissions
        $permissions = [
            'manageBusinesses' => 'Manage Businesses',
            'manageConsumers' => 'Manage Consumers',
            'manageUsers' => 'Manage Users',
        ];

        foreach ($permissions as $name => $description) {
            $permission = $auth->createPermission($name);
            $permission->description = $description;
            $auth->add($permission);

            // Assign to admin role
            $auth->addChild($auth->getRole('admin'), $permission);
        }

    }

    public function down()
    {
        $auth->removeItem($auth->getRole('admin'));
        $auth->removeItem($auth->getRole('business'));
        $auth->removeItem($auth->getRole('consumer'));
    }
}
