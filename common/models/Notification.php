<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%notification}}".
 *
 * @property int $id
 * @property int $user_id
 * @property string|null $subject
 * @property string|null $body
 * @property string|null $data
 * @property string|null $read_at
 * @property string $created_at
 * @property string|null $updated_at
 *
 * @property User $user
 */
class Notification extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%notification}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id'], 'integer'],
            [['body', 'data'], 'string'],
            [['read_at', 'created_at', 'updated_at'], 'safe'],
            [['subject'], 'string', 'max' => 255],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'subject' => 'Subject',
            'body' => 'Body',
            'data' => 'Data',
            'read_at' => 'Read At',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
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
     * @return \common\models\queries\NotificationQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\queries\NotificationQuery(get_called_class());
    }
}
