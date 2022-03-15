<?php

namespace api\versions\v1\controllers;

use api\resources\CategoryResource;
use api\versions\v1\controllers\CustomActiveController;
use api\versions\v1\traits\ResponseHelper;
use common\models\Category;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;

class CategoryManageController extends CustomActiveController
{
    use ResponseHelper;

    public $modelClass = CategoryResource::class;
    
    public function actions()
    {
        $actions = parent::actions();
        unset($actions['create']);
        unset($actions['update']);
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
            'index', 'view', 'create', 'update', 'delete'
        ];

        $behaviors['access'] = [
            'class' => AccessControl::className(),
            'only' => ['index', 'view', 'create', 'update', 'delete'],
            'rules' => [
                [
                    'allow' => true,
                    'actions' => ['index', 'view', 'create', 'update', 'delete'],
                    'roles' => ['admin'],
                ],
            ],
        ];

        return $behaviors;
    }

    public function actionIndex() {
        return $this->sendResponse(200, 'Categoryies reterived successfully', [
            'count' => Category::find()->count(),
            'items' => Category::find()->all()
        ]);
    }

    public function actionView($id) {
        return $this->sendResponse(200, 'Category reterived successfully', $this->findModel($id));
    }

    public function actionCreate() {
        $model = new Category();
        $model->name    = Yii::$app->request->post('name');
        $model->type    = Yii::$app->request->post('type');
    
        if ($model->validate() && $model->save()) {
            return $this->sendResponse(201, 'Category created successfully', $model);
        }

        return $this->sendResponse(400, 'Cannot create Category', $model->getErrors());
    }

    public function actionUpdate($id) {
        $model = $this->findModel($id);
        $model->name  = Yii::$app->request->post('name') ?? $model->name;
        $model->type  = Yii::$app->request->post('type') ?? $model->type;    

        if ($model->validate() && $model->save()) {
            return $this->sendResponse(200, 'Category updated successfully', $model);
        } else {
            return $this->sendResponse(400, 'Cannot update Category', $model->getErrors());
        }
    }

    public function actionDelete($id) {
        $model = $this->findModel($id);
        if ($model->delete() > 0) {
            $this->response->setStatusCode(204);
        }
    }

    protected function findModel($id)
    {
        if (($model = Category::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('This category does not exist.');
    }
}