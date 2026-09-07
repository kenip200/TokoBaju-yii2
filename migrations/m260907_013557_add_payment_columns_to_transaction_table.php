<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%transaction}}`.
 */
class m260907_013557_add_payment_columns_to_transaction_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('transaction', 'paid_amount', $this->decimal(12, 2)->notNull()->defaultValue(0)->after('total'));
        $this->addColumn('transaction', 'change_amount', $this->decimal(12, 2)->notNull()->defaultValue(0)->after('paid_amount'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('transaction', 'change_amount');
        $this->dropColumn('transaction', 'paid_amount');
    }
}
