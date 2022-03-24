<?php

namespace api\versions\v1\controllers;

use api\versions\v1\traits\FileHelper;
use common\helpers\ApiRouteHelper;
use common\models\Category;
use common\models\Order;
use common\models\Product;
use Yii;
use yii\filters\AccessControl;
use yii\web\UploadedFile;

class ManagmentController extends BaseController
{
    use FileHelper;

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator']['only'] = ApiRouteHelper::getActionsList(__FILE__);

        $behaviors['access'] = [
            'class' => AccessControl::className(),
            'rules' => [
                [
                    'allow' => true,
                    'actions' => [
                        'get-products', 'show-product', 'create-product', 'get-categories', 'show-category'
                    ],
                    'roles' => ['admin', 'seller']
                ],
                [
                    'allow' => true,
                    'actions' => ['update-product', 'delete-product'],
                    'roles' => ['admin', 'seller'],
                    'matchCallback' => function ($rule, $action) {
                        return $this->isModelOwner('product') || Yii::$app->user->can('admin');
                    }
                ],
                [
                    'allow' => true,
                    'actions' => ['create-category', 'update-category', 'delete-category', 'get-orders', 'show-order', 'delete-order'],
                    'roles' => ['admin'],
                ],
            ],
        ];

        return $behaviors;
    }

    protected function verbs()
    {
        return ApiRouteHelper::generateVerbsFromController(__FILE__);
    }

    public function actionGetProducts() {
        return $this->sendResponse('Products reterived successfully', [
            'total' => Product::find()->count(),
            'items' => Product::find()->all()
        ]);
    }

    public function actionShowProduct($id) {
        return $this->sendResponse('Product reterived successfully', $this->findModel($id, self::PRODUCT));
    }

    public function actionCreateProduct() {
        $model = new Product();
        $model->setScenario(Product::SCENARIO_CREATE);

        $model->name  = Yii::$app->request->post('name');
        $model->type  = Yii::$app->request->post('type');
        $model->qty   = Yii::$app->request->post('qty');
        $model->price = Yii::$app->request->post('price');
        $model->category_id = Yii::$app->request->post('category_id');
        $model->image = UploadedFile::getInstanceByName('image');

        if (!$model->validate()) {
            return $this->sendResponse('Cannot create product, validate your input!', $model->getErrors(), 400);
        }
        
        $imgUniqueName = uniqid('pro-');
        $model->image->saveAs('@storage/uploads' . '/' . $imgUniqueName . '.' . $model->image->extension);
        $model->image = $imgUniqueName . '.' . $model->image->extension;
        $model->save();
        return $this->sendResponse('Product created successfully', $model, 201);
    }

    public function actionUpdateProduct($id) {
        $model = $this->findModel($id, self::PRODUCT);
        $model->scenario = Product::SCENARIO_UPDATE;

        $oldImage = $model->image;
        
        $model->name  = Yii::$app->request->post('name') ?? $model->name;
        $model->type  = Yii::$app->request->post('type') ?? $model->type;
        $model->qty   = Yii::$app->request->post('qty') ?? $model->qty;
        $model->price = Yii::$app->request->post('price') ?? $model->price;
        $model->category_id = Yii::$app->request->post('category_id') ?? $model->category_id;
        
        $newImage = UploadedFile::getInstanceByName('image');
        $model->image = $newImage ?? $model->image;

        if (!$model->validate()) {
            return $this->sendResponse('Cannot update product', $model->getErrors(), 400);
        }

        if($newImage != null) {
            $imgUniqueName = uniqid('pro-');
            $newImage->saveAs('@storage/uploads' . '/' . $imgUniqueName . '.' . $newImage->extension);
            $model->image = $imgUniqueName . '.' . $model->image->extension;                
            $this->deleteFile(Yii::getAlias('@storage/uploads') . '/' . $oldImage);
        }

        $model->save();

        return $this->sendResponse('Product updated successfully', $model);
    }

    public function actionDeleteProduct($id) {
        $model = $this->findModel($id, self::PRODUCT);
        $model->scenario = Product::SCENARIO_DELETE;
        if ($model->delete() > 0) {
            $this->deleteFile(Yii::getAlias('@storage/uploads') . '/' . $model->image);
            $this->response->setStatusCode(204);
        }
    }

    public function actionGetCategories() {
        return $this->sendResponse('Categoryies reterived successfully', [
            'total' => Category::find()->count(),
            'items' => Category::find()->all()
        ]);
    }

    public function actionShowCategory($id) {
        return $this->sendResponse('Category reterived successfully', $this->findModel($id, self::CATEGORY));
    }

    public function actionCreateCategory() {
        $model = new Category();
        $model->name    = Yii::$app->request->post('name');
        $model->type    = Yii::$app->request->post('type');
    
        if ($model->validate() && $model->save()) {
            return $this->sendResponse('Category created successfully', $model, 201);
        }

        return $this->sendResponse('Cannot create Category', $model->getErrors(), 400);
    }

    public function actionUpdateCategory($id) {
        $model = $this->findModel($id, self::CATEGORY);
        $model->name  = Yii::$app->request->post('name') ?? $model->name;
        $model->type  = Yii::$app->request->post('type') ?? $model->type;    

        if ($model->validate() && $model->save()) {
            return $this->sendResponse('Category updated successfully', $model);
        }

        return $this->sendResponse('Cannot update Category', $model->getErrors(), 400);
    }

    public function actionDeleteCategory($id) {
        if ($this->findModel($id, self::CATEGORY)->delete() > 0) {
            $this->response->setStatusCode(204);
        }
    }

    public function actionGetOrders() {
        return $this->sendResponse('Orders reterived successfully', [
            'total' => Order::find()->count(),
            'items' => Order::find()->all()
        ]);
    }

    public function actionShowOrder($id) {
        return $this->sendResponse('Order reterived successfully', $this->findModel($id, self::ORDER));
    }

    public function actionDeleteOrder($id) {
        if ($this->findModel($id, self::ORDER)->delete() > 0) {
            $this->response->setStatusCode(204);
        }
    }
}