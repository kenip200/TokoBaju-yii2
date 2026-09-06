<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "transaction_item".
 *
 * @property int $id
 * @property int $transaction_id
 * @property int $product_id
 * @property int $qty
 * @property float $price
 * @property float $subtotal
 * @property string|null $created_at
 *
 * @property Product $product
 * @property Transaction $transaction
 */
class Transaction_item extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'transaction_item';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['qty'], 'default', 'value' => 1],
            [['subtotal'], 'default', 'value' => 0.00],
            [['transaction_id', 'product_id'], 'required'],
            [['transaction_id', 'product_id', 'qty'], 'integer'],
            [['price', 'subtotal'], 'number'],
            [['created_at'], 'safe'],
            [['product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Product::class, 'targetAttribute' => ['product_id' => 'id']],
            [['transaction_id'], 'exist', 'skipOnError' => true, 'targetClass' => Transaction::class, 'targetAttribute' => ['transaction_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'transaction_id' => 'Transaction ID',
            'product_id' => 'Product ID',
            'qty' => 'Qty',
            'price' => 'Price',
            'subtotal' => 'Subtotal',
            'created_at' => 'Created At',
        ];
    }

    /**
     * Gets query for [[Product]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::class, ['id' => 'product_id']);
    }

    /**
     * Gets query for [[Transaction]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTransaction()
    {
        return $this->hasOne(Transaction::class, ['id' => 'transaction_id']);
    }

}
