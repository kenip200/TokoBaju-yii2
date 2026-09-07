<?php

use yii\db\Migration;

class m260907_000926_add_image_description_to_product extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('product', 'description', $this->text()->null());
        $this->addColumn('product', 'image', $this->string(255)->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('product', 'image');
        $this->dropColumn('product', 'description');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m260907_000926_add_image_description_to_product cannot be reverted.\n";

        return false;
    }
    */
}
