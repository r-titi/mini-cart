<?php

namespace api\versions\v1\controllers;

use api\helpers\File;
use api\versions\v1\controllers\CustomActiveController;
use api\versions\v1\traits\FileHelper;
use api\versions\v1\traits\ResponseHelper;
use common\models\Product;
use Yii;
use yii\filters\AccessControl;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

class ProductManageController extends CustomActiveController
{
    use FileHelper, ResponseHelper;

    public $modelClass = 'common\models\Product';
    
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
                    'roles' => ['admin', 'seller'],
                ],
            ],
        ];

        return $behaviors;
    }

    public function actionIndex() {
        return $this->sendResponse(200, 'Products reterived successfully', [
            'total' => Product::find()->count(),
            'items' => Product::find()->all()
        ]);
    }

    public function actionView($id) {
        return $this->sendResponse(200, 'Product reterived successfully', $this->findModel($id));
    }

    public function actionCreate() {
        $model = new Product();
        $model->setScenario(Product::SCENARIO_CREATE);

        $model->user_id = Yii::$app->user->id;
        $model->name  = Yii::$app->request->post('name');
        $model->type  = Yii::$app->request->post('type');
        $model->qty   = Yii::$app->request->post('qty');
        $model->price = Yii::$app->request->post('price');
        $model->category_id = Yii::$app->request->post('category_id');
        $model->image = UploadedFile::getInstanceByName('image');

        if ($model->validate()) {
            $imgUniqueName = uniqid('pro-');
            $model->image->saveAs('@frontend/uploads' . '/' . $imgUniqueName . '.' . $model->image->extension);
            $model->image = $imgUniqueName . '.' . $model->image->extension;
            $model->save();

            return $this->sendResponse(201, 'Product created successfully', $model);
        } else {
            return $this->sendResponse(400, 'Cannot create product, validate your input!', $model->getErrors());
        }
    }

    public function actionUpdate($id) {
        $model = $this->findModel($id);
        $model->scenario = Product::SCENARIO_UPDATE;
        $this->checkAccess('update', $model);

        $oldImage = $model->image;
        
        $model->name  = Yii::$app->request->post('name') ?? $model->name;
        $model->type  = Yii::$app->request->post('type') ?? $model->type;
        $model->qty   = Yii::$app->request->post('qty') ?? $model->qty;
        $model->price = Yii::$app->request->post('price') ?? $model->price;
        $model->category_id = Yii::$app->request->post('category_id') ?? $model->category_id;
        
        $newImage = UploadedFile::getInstanceByName('image');
        $model->image = $newImage ?? $model->image;

        if ($model->validate()) {
            if($newImage != null) {
                $imgUniqueName = uniqid('pro-');
                $newImage->saveAs('@frontend/uploads' . '/' . $imgUniqueName . '.' . $newImage->extension);
                $model->image = $imgUniqueName . '.' . $model->image->extension;                
                $this->deleteFile(Yii::getAlias('@frontend/uploads') . '/' . $oldImage);
            }

            $model->save();

            return $this->sendResponse(200, 'Product updated successfully', $model);
        } else {
            return $this->sendResponse(400, 'Cannot update product', $model->getErrors());
        }
    }

    public function actionDelete($id) {
        $model = $this->findModel($id);
        $model->scenario = Product::SCENARIO_UPDATE;
        $this->checkAccess('delete', $model);
        if ($model->delete() > 0) {
            $this->deleteFile(Yii::getAlias('@frontend/uploads') . '/' . $model->image);
            $this->response->setStatusCode(204);
        }
    }

    protected function findModel($id)
    {
        if (($model = Product::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('This product does not exist.');
    }

    public function checkAccess($action, $model = null, $params = [])
    {
        if(in_array($action, ['update', 'delete']) && !Yii::$app->user->can('admin') && $model->user_id != Yii::$app->user->id) {
            throw new ForbiddenHttpException('You dont have permission to manage this record');
        }
    }
}