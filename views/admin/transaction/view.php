<?php

use yii\helpers\Html;

/** @var app\models\Transaction $transaction */
/** @var app\models\TransactionItem[] $items */

$this->title = 'Detail Transaksi: ' . $transaction->code;
?>
<h1><?= Html::encode($this->title) ?></h1>

<p><strong>User:</strong> <?= Html::encode($transaction->user_name) ?></p>
<p><strong>Tanggal:</strong> <?= Yii::$app->formatter->asDatetime($transaction->created_at) ?></p>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Produk</th>
            <th>Harga</th>
            <th>Qty</th>
            <th>Subtotal</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($items as $item): ?>
        <tr>
            <td><?= $item->product ? Html::encode($item->product->name) : '(produk dihapus)' ?></td>
            <td>Rp <?= number_format($item->price, 0, ',', '.') ?></td>
            <td><?= $item->qty ?></td>
            <td>Rp <?= number_format($item->subtotal, 0, ',', '.') ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<table class="table table-sm w-auto">
    <tr><th>Total Belanja</th><td>Rp <?= number_format($transaction->total, 0, ',', '.') ?></td></tr>
    <tr><th>Jumlah Bayar</th><td>Rp <?= number_format($transaction->paid_amount, 0, ',', '.') ?></td></tr>
    <tr><th>Kembalian</th><td>Rp <?= number_format($transaction->change_amount, 0, ',', '.') ?></td></tr>
</table>

<?= Html::a('&laquo; Kembali', ['admin/transaction/index'], ['class' => 'btn btn-secondary']) ?>