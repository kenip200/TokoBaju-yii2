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
        <a href="<?= Url::to(['product/view', 'id' => $product->id]) ?>" class="text-decoration-none text-dark">
            <div class="card h-100">
                <?php if (!empty($product->image)): ?>
                    <img src="<?= Yii::getAlias('@web/uploads/products/' . $product->image) ?>"
                         class="card-img-top"
                         alt="<?= Html::encode($product->name) ?>"
                         style="height: 200px; object-fit: cover;">
                <?php else: ?>
                    <div class="d-flex align-items-center justify-content-center bg-light"
                         style="height: 200px;">
                        <span class="text-muted">Tidak ada gambar</span>
                    </div>
                <?php endif; ?>
                <div class="card-body">
                    <h5 class="card-title"><?= Html::encode($product->name) ?></h5>
                    <p class="card-text">
                        Rp <?= number_format($product->price, 0, ',', '.') ?><br>
                        Stok: <?= $product->stock ?>
                    </p>
                </div>
            </div>
        </a>
    </div>
<?php endforeach; ?>
</div>

<?= LinkPager::widget([
    'pagination' => $dataProvider->pagination,
]) ?>