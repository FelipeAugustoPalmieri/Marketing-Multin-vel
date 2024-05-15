<?php

use yii\db\Migration;

class m170308_133322_insert_representative_role extends Migration
{
    public function up()
    {
        $auth = Yii::$app->authManager;

        // Roles
        $roles = [
            'representative' => 'Representative',
        ];

        foreach ($roles as $name => $description) {
            $role = $auth->createRole($name);
            $role->description = $description;
            $auth->add($role);
        }

        // Permissions
        $permissions = [
            'viewRepresentativeCities' => 'View Representative Cities',
        ];

        foreach ($permissions as $name => $description) {
            $permission = $auth->createPermission($name);
            $permission->description = $description;
            $auth->add($permission);

            $auth->addChild($auth->getRole('representative'), $permission);
        }

    }

    public function down()
    {
        $auth->removeItem($auth->getRole('representative'));
    }
}
