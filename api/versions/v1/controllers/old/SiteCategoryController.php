<?php

namespace api\versions\v1\controllers;

use api\versions\v1\traits\ResponseHelper;
use common\models\Category;
use common\models\Product;
use yii\filters\AccessControl;
use yii\rest\ActiveController;
use yii\web\NotFoundHttpException;

class SiteCategoryController extends ActiveController
{
    use ResponseHelper;
    
    public $modelClass = 'common\models\Category';
    
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
        return $this->sendResponse(200, 'Categories reterived successfully', [
            'count' => Category::find()->count(),
            'items' => Category::find()->all()
        ]);
    }

    public function actionView($id) {    
        return $this->sendResponse(200, 'Category reterived successfully', $this->findModel($id));
    }

    public function actionProducts($id) {
        return $this->sendResponse(200, 'Category products reterived successfully', [
            'count' => Product::find()->where(['category_id' => $id])->count(),
            'items' => Product::find()->where(['category_id' => $id])->all()
        ]);
    }

    protected function findModel($id)
    {
        if (($model = Category::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('This category does not exist.');
    }
}