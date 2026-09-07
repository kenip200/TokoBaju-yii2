<?php
use yii\helpers\Html;

$this->title = 'Konfirmasi Pembayaran';
?>
<h1><?= Html::encode($this->title) ?></h1>

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
                <td><?= Html::encode($item['product']->name) ?></td>
                <td>Rp <?= number_format($item['product']->price, 0, ',', '.') ?></td>
                <td><?= $item['qty'] ?></td>
                <td>Rp <?= number_format($item['subtotal'], 0, ',', '.') ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <th colspan="3" class="text-end">Total</th>
            <th>Rp <?= number_format($total, 0, ',', '.') ?></th>
        </tr>
    </tfoot>
</table>

<?= Html::beginForm(['transaction/checkout'], 'post', ['id' => 'form-bayar']) ?>

    <div class="mb-3" style="max-width: 300px;">
        <label class="form-label">Jumlah Bayar</label>
        <?= Html::textInput('bayar', Yii::$app->request->post('bayar', ''), [
            'class' => 'form-control',
            'type' => 'number',
            'min' => $total,
            'step' => '1',
            'id' => 'input-bayar',
            'required' => true,
            'placeholder' => 'Masukkan nominal uang diterima',
        ]) ?>
    </div>

    <div class="mb-3" style="max-width: 300px;">
        <label class="form-label">Kembalian</label>
        <input type="text" class="form-control" id="preview-kembalian" readonly value="Rp 0">
    </div>

    <?= Html::submitButton('Konfirmasi & Bayar', ['class' => 'btn btn-success']) ?>
    <?= Html::a('Batal', ['cart/index'], ['class' => 'btn btn-secondary']) ?>

<?= Html::endForm() ?>

<script>
    // Preview kembalian real-time di sisi client, perhitungan asli tetap di server
    (function () {
        var total = <?= (int) $total ?>;
        var inputBayar = document.getElementById('input-bayar');
        var previewKembalian = document.getElementById('preview-kembalian');

        function formatRupiah(angka) {
            return 'Rp ' + angka.toLocaleString('id-ID');
        }

        inputBayar.addEventListener('input', function () {
            var bayar = parseInt(this.value) || 0;
            var kembalian = bayar - total;
            previewKembalian.value = kembalian >= 0 ? formatRupiah(kembalian) : 'Kurang bayar';
        });
    })();
</script>