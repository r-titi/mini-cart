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

    public const PRODUCT = 'product';
    public const CATEGORY = 'category';
    public const CART = 'cart';
    public const ORDER = 'order';

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

    protected function findModel($id, $type)
    {
        $model = null;

        switch($type) {
            case self::PRODUCT:
                $model = Product::findOne(['id' => $id]);
                break;
            case self::CATEGORY:
                $model = Category::findOne(['id' => $id]);
                break;
            case self::ORDER:
                $model = Order::findOne(['id' => $id]);
                break;
            case self::CART:
                $model = Cart::findOne(['id' => $id]);
                break;
        }
        
        if($model != null)
            return $model;

        throw new NotFoundHttpException('This ' . $type . ' does not exist.');
    }
}