<?php

use yii\web\JsonParser;

$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-api',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'api\controllers',
    'modules' => [
        'v1' => [
          'class' => 'api\versions\v1\Module'
        ],
    ],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-api',
            'parsers' => [
                'application/json' => JsonParser::class,
                'multipart/form-data' => 'yii\web\MultipartFormDataParser'
            ]
        ],
        'response' => [
            'class' => 'yii\web\Response',
            'format' => yii\web\Response::FORMAT_JSON,
            'on beforeSend' => function ($event) {
                $response = $event->sender;
                if ($response->data !== null) {
                    $response->data['success'] = $response->isSuccessful;
                    
                    if(array_key_exists('data', $response->data)) {
                        $response->data['data'] = $response->data['data'];
                    }

                    // if(array_key_exists('data', $response->data)) {
                    //     if(array_key_exists('total', $response->data['data'])){
                    //         $response->data['total'] = $response->data['data']['total'];
                    //         $response->data['data'] = $response->data['data']['items'];
                    //     }
                    // }

                    if(array_key_exists('message', $response->data)) {
                        $response->data['message'] = $response->data['message'];
                    }
                }
            },
        ],
        'user' => [
            'enableSession' => false,
            'loginUrl' => null,
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-api', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the api
            'name' => 'advanced-api',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            // 'enableStrictParsing'   => false,
            'rules' => [
                '' => 'v1/site/get-products',
                'v1' => 'v1/site/get-products',
                'v1/site' => 'v1/site/get-products',
                'POST v1/login' => 'v1/auth/login',
                
                //??? not work
                // '<version:\w+>/<controller:\w+>/<action:\w+>/' => '<version>/<controller>/<action>',
                // '<version:\w+>/<controller:\w+>/<action:\w+>/<id:\d+>' => '<version>/<controller>/<action>',
                
                //site routes
                'GET v1/site/product' => 'v1/site/get-products',
                'GET v1/site/product/<id:\d+>' => 'v1/site/show-product',
                'GET v1/site/category' => 'v1/site/get-categories',
                'GET v1/site/category/<id:\d+>' => 'v1/site/show-category',
                'GET v1/site/category/<id:\d+>/products' => 'v1/site/get-category-products',
                'GET v1/site/cart' => 'v1/site/get-cart',
                'POST v1/site/cart' => 'v1/site/add-to-cart',
                'DELETE v1/site/cart/<id:\d+>' => 'v1/site/remove-from-cart',
                'DELETE v1/site/cart' => 'v1/site/clear-cart',

                //manage routes
                'GET v1/managment/product' => 'v1/managment/get-products',
                'GET v1/managment/product/<id:\d+>' => 'v1/managment/show-product',
                'POST v1/managment/product' => 'v1/managment/create-product',
                'PUT v1/managment/product/<id:\d+>' => 'v1/managment/update-product',
                'DELETE v1/managment/product/<id:\d+>' => 'v1/managment/delete-product',
                'GET v1/managment/category' => 'v1/managment/get-categories',
                'GET v1/managment/category/<id:\d+>' => 'v1/managment/show-category',
                'POST v1/managment/category' => 'v1/managment/create-category',
                'PUT v1/managment/category/<id:\d+>' => 'v1/managment/update-category',
                'DELETE v1/managment/category/<id:\d+>' => 'v1/managment/delete-category',
                'GET v1/managment/order' => 'v1/managment/get-orders',
                'GET v1/managment/order/<id:\d+>' => 'v1/managment/show-order',
                'DELETE v1/managment/order/<id:\d+>' => 'v1/managment/delete-order',


                //default routes
                'v1/site/show-product/<id:\d+>' => 'v1/site/show-product',
                'v1/site/show-category/<id:\d+>' => 'v1/site/show-category',
                'v1/site/get-category-products/<id:\d+>' => 'v1/site/get-category-products',
                'v1/site/remove-from-cart/<id:\d+>' => 'v1/site/remove-from-cart',
                'v1/managment/show-product/<id:\d+>' => 'v1/managment/show-product',
                'v1/managment/update-product/<id:\d+>' => 'v1/managment/update-product',
                'v1/managment/delete-product/<id:\d+>' => 'v1/managment/delete-product',
                'v1/managment/show-category/<id:\d+>' => 'v1/managment/show-category',
                'v1/managment/update-category/<id:\d+>' => 'v1/managment/update-category',
                'v1/managment/delete-category/<id:\d+>' => 'v1/managment/delete-category',
                'v1/managment/show-order/<id:\d+>' => 'v1/managment/show-order',
                'v1/managment/delete-order/<id:\d+>' => 'v1/managment/delete-order',
            ],
        ],
    ],
    'params' => $params,
];
