<?php

namespace api\versions\v1\controllers;

use api\versions\v1\traits\ResponseHelper;
use common\models\Cart;
use common\models\Category;
use common\models\Order;
use common\models\Product;
use Yii;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use yii\rest\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class BaseController extends Controller
{
    use ResponseHelper;

    public const PRODUCT = 'common\models\Product';
    public const CATEGORY = 'common\models\Category';
    public const CART = 'common\models\Cart';
    public const ORDER = 'common\models\Order';

    public $serializer = [
        'class' => 'yii\rest\Serializer',
        'collectionEnvelope' => 'items',
    ];

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => CompositeAuth::class,
            'authMethods' => [
                HttpBasicAuth::class,
                HttpBearerAuth::class,
                QueryParamAuth::class,
            ],
        ];
        $behaviors['contentNegotiator'] = [
            'class' => 'yii\filters\ContentNegotiator',
            'formats' => ['application/json' => Response::FORMAT_JSON]
        ];
        return $behaviors;
    }

    public function checkAccess($action, $model = null, $params = [])
    {
        if(in_array($action, ['update', 'delete']) && !Yii::$app->user->can('admin') && $model->user_id != Yii::$app->user->id) {
            throw new ForbiddenHttpException('You dont have permission to manage this record');
        }
    }

    protected function isModelOwner($type)
    {
        return $this->findModel(Yii::$app->request->get('id'), $type)->user_id === Yii::$app->user->id;
    }

    protected function findModel($id, $type)
    {
        $model = $type::findOne(['id' => $id]);
        
        if($model != null)
            return $model;

        throw new NotFoundHttpException('This ' . $type . ' does not exist.');
    }
}