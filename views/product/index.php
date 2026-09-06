<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap5\LinkPager;

$this->title = 'Produk';
?>
<h1><?= Html::encode($this->title) ?></h1>

<?= Html::beginForm(['product/index'], 'get', ['class' => 'row g-2 mb-4']) ?>

    <div class="col-md-4">
        <?= Html::dropDownList(
            'category_id',
            $categoryId,
            \yii\helpers\ArrayHelper::map($categories, 'id', 'name'),
            ['prompt' => 'Semua Kategori', 'class' => 'form-select', 'onchange' => 'this.form.submit()']
        ) ?>
    </div>
    <div class="col-md-6">
        <?= Html::textInput('search', $search, [
            'class' => 'form-control',
            'placeholder' => 'Cari nama produk...'
        ]) ?>
    </div>
    <div class="col-md-2">
        <?= Html::submitButton('Cari', ['class' => 'btn btn-primary w-100']) ?>
    </div>

<?= Html::endForm() ?>

<div class="row">
<?php foreach ($dataProvider->getModels() as $product): ?>
    <div class="col-md-3 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title"><?= Html::encode($product->name) ?></h5>
                <p class="card-text">
                    Rp <?= number_format($product->price, 0, ',', '.') ?><br>
                    Stok: <?= $product->stock ?>
                </p>
                <?= Html::a('Tambah ke Keranjang',
                    Url::to(['cart/add', 'id' => $product->id]),
                    ['class' => 'btn btn-sm btn-success', 'data-method' => 'post']
                ) ?>
            </div>
        </div>
    </div>
<?php endforeach; ?>
</div>

<?= LinkPager::widget([
    'pagination' => $dataProvider->pagination,
]) ?>