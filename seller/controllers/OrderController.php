<?php

namespace seller\controllers;

use common\components\Helpers;
use common\models\Order;
use common\models\OrderItem;
use common\models\User;
use Yii;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\VarDumper;
use yii\web\Controller;

class OrderController extends Controller {

    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'access' => [
                    'class' => AccessControl::className(),
                    'only' => ['index'], //only be applied to
                    'rules' => [
                        [
                            'allow' => true,
                            'actions' => ['index'],
                            'roles' => ['seller'],
                        ],
                    ],
                ],
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        // 'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }
    
    public function actionIndex() {
        $items = OrderItem::find()->joinWith([
            'product' => function($q) {
                $q->where(['user_id' => Yii::$app->user->id]);
            }
        ])->all();

        $dataProvider = new ArrayDataProvider([
            'allModels' => $items,
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

        // $dataProvider = new ActiveDataProvider([
        //     'query' => $items,
        //     /*
        //     'pagination' => [
        //         'pageSize' => 50
        //     ],
        //     'sort' => [
        //         'defaultOrder' => [
        //             'id' => SORT_DESC,
        //         ]
        //     ],
        //     */
        // ]);

        // Helpers::dd($dataProvider);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }
}