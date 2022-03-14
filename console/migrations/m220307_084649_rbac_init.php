<?php

use yii\db\Migration;

/**
 * Class m220307_084649_rbac_init
 */
class m220307_084649_rbac_init extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // $auth = Yii::$app->authManager;

        // $manageCars = $auth->createPermission('manageCars');
        // $manageCars->description = 'Manage Cars';
        // $auth->add($manageCars);

        // $manageParts = $auth->createPermission('manageParts');
        // $manageParts->description = 'Manage Parts';
        // $auth->add($manageParts);

        // $manageUsers = $auth->createPermission('manageUsers');
        // $manageUsers->description = 'Manage users';
        // $auth->add($manageUsers);

        // $seller = $auth->createRole('seller');
        // $seller->description = 'Seller';
        // $auth->add($seller);
        // $auth->addChild($seller, $manageCars);
        // $auth->addChild($seller, $manageParts);

        // $admin = $auth->createRole('admin');
        // $admin->description = 'Administrator';
        // $auth->add($admin);
        // $auth->addChild($admin, $seller);
        // $auth->addChild($admin, $manageUsers);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        Yii::$app->authManager->removeAll();
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220307_084649_rbac_init cannot be reverted.\n";

        return false;
    }
    */
}
