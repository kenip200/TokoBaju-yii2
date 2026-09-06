<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%transaction_item}}`.
 */
class m260906_115807_create_transaction_item_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%transaction_item}}', [
            'id' => $this->primaryKey(),
            'transaction_id' => $this->integer()->notNull(),
            'product_id' => $this->integer()->notNull(),
            'qty' => $this->integer()->notNull()->defaultValue(1),
            'price' => $this->decimal(15, 2)->notNull()->defaultValue(0),
            'subtotal' => $this->decimal(15, 2)->notNull()->defaultValue(0),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        $this->createIndex('idx-transaction_item-transaction_id', '{{%transaction_item}}', 'transaction_id');
        $this->createIndex('idx-transaction_item-product_id', '{{%transaction_item}}', 'product_id');

        $this->addForeignKey(
            'fk-transaction_item-transaction_id',
            '{{%transaction_item}}',
            'transaction_id',
            '{{%transaction}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-transaction_item-product_id',
            '{{%transaction_item}}',
            'product_id',
            '{{%product}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-transaction_item-transaction_id', '{{%transaction_item}}');
        $this->dropForeignKey('fk-transaction_item-product_id', '{{%transaction_item}}');
        $this->dropIndex('idx-transaction_item-transaction_id', '{{%transaction_item}}');
        $this->dropIndex('idx-transaction_item-product_id', '{{%transaction_item}}');
        $this->dropTable('{{%transaction_item}}');
    }
}
