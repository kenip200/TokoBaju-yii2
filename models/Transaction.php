<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "transaction".
 *
 * @property int $id
 * @property string $user_name
 * @property string $code
 * @property float $total
 * @property string|null $created_at
 *
 * @property TransactionItem[] $transactionItems
 */
class Transaction extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'transaction';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['total'], 'default', 'value' => 0.00],
            [['user_name', 'code'], 'required'],
            [['total'], 'number'],
            [['created_at'], 'safe'],
            [['user_name'], 'string', 'max' => 100],
            [['code'], 'string', 'max' => 50],
            [['code'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_name' => 'User Name',
            'code' => 'Code',
            'total' => 'Total',
            'created_at' => 'Created At',
        ];
    }

    /**
     * Gets query for [[TransactionItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTransactionItems()
    {
        return $this->hasMany(TransactionItem::class, ['transaction_id' => 'id']);
    }

    public function getProducts()
    {
        return $this->hasMany(Product::class, ['id' => 'product_id'])
            ->via('transactionItems');
    }

}
