<?php

namespace api\resources;

use common\models\Category;
use common\models\User;

class CategoryResource extends Category {
    public function fields()
    {
        return ['id', 'name', 'type'];
    }

    public function extraFields()
    {
        return ['created_at', 'updated_at'];
    }
}