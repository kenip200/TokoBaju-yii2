<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var array $items */
/** @var float $total */

$this->title = 'Keranjang Belanja';
?>
<h1><?= Html::encode($this->title) ?></h1>

<?php if (Yii::$app->session->hasFlash('success')): ?>
    <div class="alert alert-success"><?= Yii::$app->session->getFlash('success') ?></div>
<?php endif; ?>

<?php if (Yii::$app->session->hasFlash('error')): ?>
    <div class="alert alert-danger"><?= Yii::$app->session->getFlash('error') ?></div>
<?php endif; ?>

<?php if (empty($items)): ?>

    <p>Keranjang belanja Anda masih kosong.</p>
    <?= Html::a('Belanja Sekarang', ['product/index'], ['class' => 'btn btn-primary']) ?>

<?php else: ?>

    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead>
            <tr>
                <th>Gambar</th>
                <th>Nama Produk</th>
                <th>Harga</th>
                <th style="width:120px">Qty</th>
                <th>Subtotal</th>
                <th style="width:150px">Aksi</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($items as $item): ?>
                <?php $product = $item['product']; ?>
                <tr>
                    <td>
                        <?php if ($product->image): ?>
                            <img src="<?= Yii::getAlias('@web/uploads/products/' . $product->image) ?>"
                                 style="width:60px;height:60px;object-fit:cover;">
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                    <td><?= Html::encode($product->name) ?></td>
                    <td>Rp <?= number_format($product->price, 0, ',', '.') ?></td>
                    <td>
                        <?= Html::beginForm(['cart/update'], 'post', ['class' => 'd-flex gap-1']) ?>
                            <?= Html::hiddenInput('id', $product->id) ?>
                            <?= Html::input('number', 'qty', $item['qty'], [
                                'class' => 'form-control form-control-sm',
                                'min' => 1,
                                'max' => $product->stock,
                                'style' => 'width:70px',
                            ]) ?>
                            <?= Html::submitButton('Update', ['class' => 'btn btn-sm btn-secondary']) ?>
                        <?= Html::endForm() ?>
                    </td>
                    <td>Rp <?= number_format($item['subtotal'], 0, ',', '.') ?></td>
                    <td>
                        <?= Html::a('Hapus', ['cart/remove', 'id' => $product->id], [
                            'class' => 'btn btn-sm btn-danger',
                            'data' => [
                                'confirm' => 'Hapus produk ini dari keranjang?',
                                'method' => 'post',
                            ],
                        ]) ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
            <tfoot>
            <tr>
                <th colspan="4" style="text-align:right">Total</th>
                <th colspan="2">Rp <?= number_format($total, 0, ',', '.') ?></th>
            </tr>
            </tfoot>
        </table>
    </div>

    <div class="d-flex justify-content-between">
        <?= Html::a('Kosongkan Keranjang', ['cart/clear'], [
            'class' => 'btn btn-warning',
            'data' => [
                'confirm' => 'Kosongkan semua keranjang?',
                'method' => 'post',
            ],
        ]) ?>

        <?= Html::a('Checkout', ['transaction/checkout'], ['class' => 'btn btn-success']) ?>
    </div>

<?php endif; ?>