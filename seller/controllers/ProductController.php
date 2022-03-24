<?php

namespace seller\controllers;

use common\components\Helpers;
use common\filters\rules\OwnerAccessRule;
use common\models\Category;
use common\models\data\ProductData;
use common\models\Product;
use common\repositories\ProductRepository;
use common\services\product\CreateService;
use common\traits\FileHelper;
use seller\traits\PermissionTrait;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\UploadedFile;

/**
 * ProductController implements the CRUD actions for Product model.
 */
class ProductController extends CustomController
{
    use PermissionTrait, FileHelper;
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'ownerOnlyAccess' => [
                    'class' => AccessControl::className(),
                    'only' => ['update', 'delete'],
                    'rules' => [
                        [
                            'actions' => ['update', 'delete'],
                            'allow' => true,
                            'roles' => ['seller'],
                            'matchCallback' => function ($rule, $action) {
                                return $this->isModelOwner();
                            }
                        ],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all Product models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Product::find()->byUser(Yii::$app->user->id),
            /*
            'pagination' => [
                'pageSize' => 50
            ],
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ]
            ],
            */
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Product model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Product model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $product = new Product();
        if ($this->request->isPost) {
            $productData = new ProductData;
            $productData->load($this->request->post('Product'));
            $productData->user_id = Yii::$app->user->id;
            $productData->image = UploadedFile::getInstance($product, 'image');

            $createService = new CreateService(); 
            $product = $createService->create($productData);

            if (!$product->hasErrors()) {
                return $this->redirect(['view', 'id' => $product->id]);
            }
        } else {
            $product->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $product,
            'categories' => Category::find()->all()
        ]);
    }

    /**
     * Updates an existing Product model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->scenario = Product::SCENARIO_UPDATE;

        if ($this->request->isPost) {
            $oldImage = $model->image;
        
            $model->name  = Yii::$app->request->post('Product')['name'] ?? $model->name;
            $model->type  = Yii::$app->request->post('Product')['type'] ?? $model->type;
            $model->qty   = Yii::$app->request->post('Product')['qty'] ?? $model->qty;
            $model->price = Yii::$app->request->post('Product')['price'] ?? $model->price;
            $model->category_id = Yii::$app->request->post('Product')['category_id'] ?? $model->category_id;
            
            $newImage = UploadedFile::getInstance($model, 'image');
            $model->image = $newImage ?? $model->image;
            if($model->validate()) {
                if($newImage != null) {
                    $imgUniqueName = uniqid('pro-');
                    $newImage->saveAs('@storage/uploads' . '/' . $imgUniqueName . '.' . $newImage->extension);
                    $model->image = $imgUniqueName . '.' . $model->image->extension;                
                    $this->deleteFile(Yii::getAlias('@storage/uploads') . '/' . $oldImage);
                }
    
                $model->save();
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
            'categories' => Category::find()->all()
        ]);
    }

    /**
     * Deletes an existing Product model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }

    /**
     * Finds the Product model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Product the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Product::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
