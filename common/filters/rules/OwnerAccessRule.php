<?php

namespace common\filters\rules;

use common\models\Product;
use yii\base\InvalidConfigException;
use yii\filters\AccessRule;

class OwnerAccessRule extends AccessRule {

    private function getProductOwner($request)
    {
        return Product::findOne($request->get('id'))->user_id ?? null;
    }
}