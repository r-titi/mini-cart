<?php

namespace seller\controllers;

use common\models\Category;
use common\models\Product;
use seller\traits\PermissionTrait;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\ForbiddenHttpException;
use yii\web\UploadedFile;

/**
 * ProductController implements the CRUD actions for Product model.
 */
class ProductController extends CustomController
{
    use PermissionTrait;
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'access' => [
                    'class' => AccessControl::className(),
                    'only' => ['index', 'all', 'view', 'create', 'update', 'delete'], //only be applied to
                    'rules' => [
                        [
                            'allow' => true,
                            'actions' => ['index', 'all', 'view', 'create', 'update', 'delete'],
                            'roles' => ['seller'],
                        ],
                    ],
                ],
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
                // [
                //     'class' => BlameableBehavior::className(),
                //     'createdByAttribute' => 'user_id',
                //     'updatedByAttribute' => false,
                // ],
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

    public function actionAll()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Product::find(),
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

        return $this->render('all', [
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
        $model = new Product();

        if ($this->request->isPost) {
            $model->user_id = Yii::$app->user->id;
            $uploadPath = Yii::getAlias('@common/uploads');
            $file = UploadedFile::getInstance($model, 'image');
            if(!in_array($file->extension, ['png', 'jpg'])) {
                echo 'You should upload image only';
                exit;
            }

            if ($model->load($this->request->post()) && $model->save()) {
                $model->image = \Yii::$app->security->generateRandomString() . '.' . $file->extension;
                $file->saveAs($uploadPath . '/' . $model->image);
                $model->save();
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        $categories = Category::find()->all();

        return $this->render('create', [
            'model' => $model,
            'categories' => $categories
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
        // if (!Yii::$app->user->can('admin') && Yii::$app->user->id != $model->user_id) {
        //     throw new ForbiddenHttpException('You are not allowed to edit this product.');
        // }
        if(!$this->canEdit($model->user_id)) {
            throw new ForbiddenHttpException('You are not allowed to edit this product.');
        }

        $uploadPath = Yii::getAlias('@common/uploads');
        $file = UploadedFile::getInstance($model, 'image');
        if($file && !in_array($file->extension, ['png', 'jpg'])) {
            echo 'You should upload image only';
            exit;
        }

        $ofile = $model->image;

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            if(isset($file)) {
                $model->image = \Yii::$app->security->generateRandomString() . '.' . $file->extension;
                $file->saveAs($uploadPath . '/' . $model->image);
            } else {
                $model->image = $ofile;
            }

            $model->save();
            return $this->redirect(['view', 'id' => $model->id]);
        }

        $categories = Category::find()->all();
        return $this->render('update', [
            'model' => $model,
            'categories' => $categories
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
        $model = $this->findModel($id);
        if (!Yii::$app->user->can('admin') && Yii::$app->user->id != $model->user_id) {
            throw new ForbiddenHttpException('You are not allowed to edit this product.');
        }
        
        $model->delete();
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
