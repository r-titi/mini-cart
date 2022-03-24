<?php

namespace common\models;

use common\behaviors\AuthorBehavior;
use common\behaviors\UserBehavior;
use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%product}}".
 *
 * @property int $id
 * @property string $name
 * @property string|null $type
 * @property int|null $qty
 * @property float $price
 * @property int $user_id
 * @property int $category_id
 * @property string|null $image
 *
 * @property Category $category
 * @property OrderItem[] $orderItems
 * @property User $user
 */
class Product extends \yii\db\ActiveRecord
{
    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';
    const SCENARIO_DELETE = 'delete';

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['create'] = ['name', 'price', 'user_id', 'category_id', 'image'];
        $scenarios['update'] = ['name', 'price', 'user_id', 'category_id', 'image'];
        return $scenarios;
    }

    public function behaviors()
    {
        return [
            'author' => [
                'class' => AuthorBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_VALIDATE => 'user_id',
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%product}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'price', 'user_id', 'category_id'], 'required'],
            [['type'], 'string'],
            [['qty', 'user_id', 'category_id'], 'integer'],
            [['price'], 'number'],
            [['name'], 'string', 'max' => 255],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::className(), 'targetAttribute' => ['category_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['image'], 'required', 'on' => self::SCENARIO_CREATE],
            [['image'], 'image', 'extensions' => 'jpg, jpeg, png',],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'type' => 'Type',
            'qty' => 'Qty',
            'price' => 'Price',
            'user_id' => 'User ID',
            'category_id' => 'Category ID',
            'image' => 'Image',
        ];
    }

    /**
     * Gets query for [[Category]].
     *
     * @return \yii\db\ActiveQuery|\common\models\queries\CategoryQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['id' => 'category_id']);
    }

    /**
     * Gets query for [[OrderItems]].
     *
     * @return \yii\db\ActiveQuery|\common\models\queries\OrderItemQuery
     */
    public function getOrderItems()
    {
        return $this->hasMany(OrderItem::className(), ['product_id' => 'id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery|\common\models\queries\UserQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * {@inheritdoc}
     * @return \common\models\queries\ProductQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\queries\ProductQuery(get_called_class());
    }
}
