<?php
namespace console\controllers;

use common\models\User;
use Yii;
use yii\base\InvalidParamException;
use yii\console\Controller;

class UserController extends Controller
{
    public function actionInit()
    {
        $user = new User();
        $user->username = 'super';
        $user->email = 'super@csp.com';
        $user->password_hash = Yii::$app->security->generatePasswordHash('123456789');
        $user->generateAuthKey();
        $user->generateEmailVerificationToken();
        $user->status = 10;
        $user->save();

        $auth = Yii::$app->authManager;
        $roleObject = $auth->getRole('admin');
        if (!$roleObject) {
            throw new InvalidParamException("There is no role admin, please generate roles from rbac controller");
        }

        $auth->assign($roleObject, $user->id);

        $user = new User();
        $user->username = 'vendor';
        $user->email = 'vendor@csp.com';
        $user->password_hash = Yii::$app->security->generatePasswordHash('123456789');
        $user->generateAuthKey();
        $user->generateEmailVerificationToken();
        $user->status = 10;
        $user->save();

        $roleObject = $auth->getRole('seller');
        if (!$roleObject) {
            throw new InvalidParamException("There is no role seller, please generate roles from rbac controller");
        }

        $auth->assign($roleObject, $user->id);

        $user = new User();
        $user->username = 'user12';
        $user->email = 'user12@csp.com';
        $user->password_hash = Yii::$app->security->generatePasswordHash('123456789');
        $user->generateAuthKey();
        $user->generateEmailVerificationToken();
        $user->status = 10;
        $user->save();
    }
}