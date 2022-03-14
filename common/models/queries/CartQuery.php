<?php

namespace common\models\queries;

/**
 * This is the ActiveQuery class for [[\common\models\Cart]].
 *
 * @see \common\models\Cart
 */
class CartQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return \common\models\Cart[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \common\models\Cart|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
