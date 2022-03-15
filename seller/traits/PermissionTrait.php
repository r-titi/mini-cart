<?php

namespace seller\traits;

use Yii;

trait PermissionTrait {
    public function canEdit($model_owner) {
        if (!Yii::$app->user->can('admin') && Yii::$app->user->id != $model_owner) {
            return false;
        }

        return true;
    }
}