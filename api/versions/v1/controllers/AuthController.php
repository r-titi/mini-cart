<?php

namespace api\versions\v1\controllers;

use api\versions\v1\models\LoginForm;
use api\versions\v1\traits\ResponseHelper;
use Yii;
use yii\filters\VerbFilter;
use yii\rest\Controller;
use yii\web\Response;

class AuthController extends Controller {

    use ResponseHelper;

    // public $modelClass = 'common\models\User';

    public function behaviors()
    {
        return [
            [
            'class' => 'yii\filters\ContentNegotiator',
            'only' => ['login'],
            'formats' => ['application/json' => Response::FORMAT_JSON]
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'login'=>['post'],
                ]
            ]
        ];
    }

    public function actionLogin() {
        $model = new LoginForm();
        $model->username = Yii::$app->request->post('username');
        $model->password = Yii::$app->request->post('password');
        if ($model->login()) {
            $user = Yii::$app->user->identity;
            $user->access_token = Yii::$app->security->generateRandomString();
            $user->save();

            return $this->sendResponse(200, 'Login success', [
                'username' => $user->username,
                'access-token' => $user->access_token
            ]);
        } else {
            return $this->sendResponse(401, 'Login failed', $model->getErrors());
        }
    }
}