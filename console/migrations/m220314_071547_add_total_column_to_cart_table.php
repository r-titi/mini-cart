<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%cart}}`.
 */
class m220314_071547_add_total_column_to_cart_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%cart}}', 'total', $this->float()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%cart}}', 'total');
    }
}
