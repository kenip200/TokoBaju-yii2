<?php

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = $model->name;
?>

<p><?= Html::a('&laquo; Kembali ke Daftar Produk', ['product/index'], ['class' => 'btn btn-secondary mb-3']) ?></p>

<div class="row">
    <div class="col-md-5">
        <?php if (!empty($model->image)): ?>
            <img src="<?= Yii::getAlias('@web/uploads/products/' . $model->image) ?>"
                 class="img-fluid rounded"
                 alt="<?= Html::encode($model->name) ?>">
        <?php else: ?>
            <div class="d-flex align-items-center justify-content-center bg-light rounded"
                 style="height: 300px;">
                <span class="text-muted">Tidak ada gambar</span>
            </div>
        <?php endif; ?>
    </div>

    <div class="col-md-7">
        <h1><?= Html::encode($model->name) ?></h1>

        <p class="text-muted">
            Kategori: <?= Html::encode($model->category->name ?? '-') ?>
        </p>

        <h3 class="text-success">
            Rp <?= number_format($model->price, 0, ',', '.') ?>
        </h3>

        <p>Stok tersedia: <strong><?= $model->stock ?></strong></p>

        <hr>

        <h5>Deskripsi</h5>
        <p><?= nl2br(Html::encode($model->description)) ?: '<span class="text-muted">Tidak ada deskripsi.</span>' ?></p>

        <hr>

        <?php if ($model->stock > 0): ?>
            <?= Html::beginForm(['cart/add'], 'post') ?>
                <?= Html::hiddenInput('id', $model->id) ?>
                <div class="row g-2 align-items-center mb-3" style="max-width: 250px;">
                    <div class="col-auto">
                        <label class="col-form-label">Jumlah</label>
                    </div>
                    <div class="col-auto">
                        <?= Html::input('number', 'qty', 1, [
                            'class' => 'form-control',
                            'min' => 1,
                            'max' => $model->stock,
                        ]) ?>
                    </div>
                </div>
                <?= Html::submitButton('Tambah ke Keranjang', ['class' => 'btn btn-success btn-lg']) ?>
            <?= Html::endForm() ?>
        <?php else: ?>
            <button class="btn btn-secondary btn-lg" disabled>Stok Habis</button>
        <?php endif; ?>
    </div>
</div>