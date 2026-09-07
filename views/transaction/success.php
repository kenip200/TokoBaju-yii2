<?php
use yii\helpers\Html;

$this->title = 'Transaksi Berhasil';
?>
<h1><?= Html::encode($this->title) ?></h1>

<p>Kode Transaksi: <strong><?= Html::encode($transaction->code) ?></strong></p>
<p>Tanggal: <?= Html::encode($transaction->created_at) ?></p>

<table class="table table-bordered">
    <thead>
        <tr><th>Produk</th><th>Harga</th><th>Qty</th><th>Subtotal</th></tr>
    </thead>
    <tbody>
        <?php foreach ($items as $item): ?>
            <tr>
                <td><?= Html::encode($item->product->name) ?></td>
                <td>Rp <?= number_format($item->price, 0, ',', '.') ?></td>
                <td><?= $item->qty ?></td>
                <td>Rp <?= number_format($item->subtotal, 0, ',', '.') ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <th colspan="3" class="text-end">Total</th>
            <th>Rp <?= number_format($transaction->total, 0, ',', '.') ?></th>
        </tr>
    </tfoot>
</table>

<table class="table table-bordered" style="max-width: 400px;">
    <tr>
        <th>Total Belanja</th>
        <td>Rp <?= number_format($transaction->total, 0, ',', '.') ?></td>
    </tr>
    <tr>
        <th>Jumlah Bayar</th>
        <td>Rp <?= number_format($transaction->paid_amount, 0, ',', '.') ?></td>
    </tr>
    <tr>
        <th>Kembalian</th>
        <td>Rp <?= number_format($transaction->change_amount, 0, ',', '.') ?></td>
    </tr>
</table>

<?= Html::a('Kembali ke Produk', ['product/index'], ['class' => 'btn btn-primary']) ?>