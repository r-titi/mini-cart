<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%notification}}`.
 */
class m220317_105128_create_notification_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%notification}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'subject' => $this->string(),
            'body' => $this->text(),
            'data' => $this->text(),
            'read_at' => $this->timestamp()->null(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->null(),
        ]);

        $this->createIndex('notifiable', 'notification', ['user_id']);
        $this->addForeignKey('FK_notification_user_user_id', '{{%notification}}', 'user_id', '{{%user}}', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('notifiable', '{{%notification}}');
        $this->dropForeignKey('FK_notification_user_user_id', '{{%notification}}');
        $this->dropTable('{{%notification}}');
    }
}
