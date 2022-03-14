<?php

namespace common\models;

use api\helpers\File;
use Yii;
use yii\web\UploadedFile;

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

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['create'] = ['name', 'price', 'user_id', 'category_id', 'image'];
        $scenarios['update'] = ['name', 'price', 'user_id', 'category_id'];
        return $scenarios;
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
            [['image'], 'required'],
            // [['image'], 'imageRequired', 'skipOnEmpty' => false],
            // [['image'], 'imageType', 'skipOnEmpty' => false]
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

    // public function imageRequired($attribute, $params)
    // {
    //     if($this->scenario == self::SCENARIO_CREATE && !array_key_exists('image', $_FILES) && $_FILES['image'] == '') {
    //         $this->addError('image', 'Image cannot be blank');
    //     }
    // }

    // public function imageType($attribute, $params) {
    //     // $file = new File($_FILES['image']);
    //     if(array_key_exists('image', $_FILES)) {
    //         $file = new File($_FILES['image']);
    //         if(!$file->isImage()) {
    //             $this->addError('image', 'Image extension must be png or jpg!');
    //         }
    //     }
        
    // }

    // public function imageCheck($attribute, $params)
    // {
    //     $file = UploadedFile::getInstance($this, 'image');
    //     if ($this->scenario == self::SCENARIO_CREATE && !$file) {
    //         $this->addError('image', 'Image cannot be blank');
    //     }

    //     if (isset($file) && !in_array($file->extension, ['png', 'jpg', 'jpeg'])) {
    //         $this->addError('image', 'Image only is allowed, make sure extension is png or jpg or jpeg');
    //         // return false;
    //     }
    // }
}
