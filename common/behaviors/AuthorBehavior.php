<?php
namespace common\behaviors;

use Yii;
use yii\base\InvalidCallException;
use yii\behaviors\AttributeBehavior;
use yii\db\BaseActiveRecord;

class AuthorBehavior extends AttributeBehavior
{
    /**
     * @var string the attribute that will receive user id value
     */
    public $userIdAttribute = 'user_id';
    
    public $value;


    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        if (empty($this->attributes)) {
            $this->attributes = [
                BaseActiveRecord::EVENT_BEFORE_INSERT => [$this->userIdAttribute],
            ];
        }
    }

    protected function getValue($event)
    {
        if ($this->value === null) {
            return Yii::$app->user->id;
        }

        return parent::getValue($event);
    }

    /**
     * Updates a author attribute.
     *
     * ```php
     * $model->touch('lastVisit');
     * ```
     * @param string $attribute the name of the attribute to update.
     * @throws InvalidCallException if owner is a new record (since version 2.0.6).
     */
    public function touch($attribute)
    {
        /* @var $owner BaseActiveRecord */
        $owner = $this->owner;
        if ($owner->getIsNewRecord()) {
            throw new InvalidCallException('Updating the author is not possible on a new record.');
        }
        $owner->updateAttributes(array_fill_keys((array) $attribute, $this->getValue(null)));
    }
}
