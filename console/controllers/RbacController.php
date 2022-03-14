<?php
namespace console\controllers;

use common\models\User;
use Yii;
use yii\base\InvalidParamException;
use yii\console\Controller;

class RbacController extends Controller
{
    public function actionInit()
    {
        if (!$this->confirm("Are you sure? It will re-create permissions tree.")) {
            return self::EXIT_CODE_NORMAL;
        }

        $auth = Yii::$app->authManager;
        $auth->removeAll();

        $manageCars = $auth->createPermission('manageCars');
        $manageCars->description = 'Manage Cars';
        $auth->add($manageCars);

        $manageParts = $auth->createPermission('manageParts');
        $manageParts->description = 'Manage Parts';
        $auth->add($manageParts);

        $manageUsers = $auth->createPermission('manageUsers');
        $manageUsers->description = 'Manage users';
        $auth->add($manageUsers);

        $seller = $auth->createRole('seller');
        $seller->description = 'Seller';
        $auth->add($seller);
        $auth->addChild($seller, $manageCars);
        $auth->addChild($seller, $manageParts);

        $admin = $auth->createRole('admin');
        $admin->description = 'Administrator';
        $auth->add($admin);
        $auth->addChild($admin, $seller);
        $auth->addChild($admin, $manageUsers);
    }

    public function actionAssign($role, $username)
    {
        $user = User::find()->where(['username' => $username])->one();
        if (!$user) {
            throw new InvalidParamException("There is no user \"$username\".");
        }

        $auth = Yii::$app->authManager;
        $roleObject = $auth->getRole($role);
        if (!$roleObject) {
            throw new InvalidParamException("There is no role \"$role\".");
        }

        $auth->assign($roleObject, $user->id);
    }
}