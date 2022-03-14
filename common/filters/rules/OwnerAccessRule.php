<?php

namespace common\filters\rules;

use common\models\Product;
use yii\filters\AccessRule;

class OwnerAccessRule extends AccessRule {
    public $allow = true; 
    public $roles = ['@'];

    public function allows($action, $user, $request)
    {
        return $this->getProductOwner($request);
        $parentRes = parent::allows($action, $user, $request);
        if ($parentRes !== true) {
            return $parentRes;
        }
        return ($this->getProductOwner($request) == $user->id) || $user->can('admin');
    }

    private function getProductOwner($request)
    {
        return Product::findOne($request->get('id'))->user_id ?? null;
    }
}