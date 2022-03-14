<?php

namespace api\versions\v1\controllers;

use api\helpers\File;
use api\versions\v1\controllers\CustomActiveController;
use api\versions\v1\traits\FileHelper;
use api\versions\v1\traits\ResponseHelper;
use common\models\Order;
use common\models\Product;
use Yii;
use yii\filters\AccessControl;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

class OrderManageController extends CustomActiveController
{
    use ResponseHelper;

    public $modelClass = 'common\models\Order';
    
    public function actions()
    {
        $actions = parent::actions();
        unset($actions['delete']);
        unset($actions['index']);
        unset($actions['view']);
        return $actions;
    }

    public $serializer = [
        'class' => 'yii\rest\Serializer',
        'collectionEnvelope' => 'items',
    ];

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator']['only'] = [
            'index', 'view', 'delete'
        ];

        $behaviors['access'] = [
            'class' => AccessControl::className(),
            'only' => ['index', 'view', 'delete'],
            'rules' => [
                [
                    'allow' => true,
                    'actions' => ['index', 'view', 'delete'],
                    'roles' => ['admin'],
                ],
            ],
        ];

        return $behaviors;
    }

    public function actionIndex() {
        return $this->sendResponse(200, 'Orders reterived successfully', [
            'count' => Order::find()->count(),
            'items' => Order::find()->all()
        ]);
    }

    public function actionView($id) {
        return $this->sendResponse(200, 'Order reterived successfully', $this->findModel($id));
    }

    public function actionDelete($id) {
        $model = $this->findModel($id);
        if ($model->delete() > 0) {
            $this->response->setStatusCode(204);
        }
    }

    protected function findModel($id)
    {
        if (($model = Order::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('This product does not exist.');
    }
}