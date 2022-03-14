<?php

namespace api\versions\v1\controllers;

use api\versions\v1\traits\ResponseHelper;
use common\models\Product;
use yii\rest\ActiveController;
use yii\web\NotFoundHttpException;

class SiteProductController extends ActiveController
{
    use ResponseHelper;
    
    public $modelClass = 'common\models\Product';
    
    public function actions()
    {
        $actions = parent::actions();
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

        return $behaviors;
    }

    public function actionIndex() {    
        return $this->sendResponse(200, 'Products reterived successfully', [
            'count' => Product::find()->count(),
            'items' => Product::find()->all()
        ]);
    }

    public function actionView($id) {    
        return $this->sendResponse(200, 'Product reterived successfully', $this->findModel($id));
    }

    protected function findModel($id)
    {
        if (($model = Product::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('This product does not exist.');
    }
}