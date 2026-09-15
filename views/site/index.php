<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var string|null $username */
/** @var app\models\Product[] $topSale */
/** @var app\models\Product[] $recentProducts */

$this->title = 'TokoBaju';
?>
<div class="site-index">

    <!-- Welcome Section -->
    <div class="jumbotron text-center bg-body-secondary p-4 rounded mb-4">
        <?php if ($username): ?>
            <h2 class="text-body-emphasis">Selamat datang, <?= Html::encode($username) ?>!</h2>
            <p class="lead text-body-secondary">Temukan koleksi pakaian terbaik untukmu hari ini.</p>
        <?php else: ?>
            <h2 class="text-body-emphasis">Selamat Datang di TokoBaju</h2>
            <p class="lead text-body-secondary">Belanja pakaian berkualitas, mudah dan cepat.</p>
        <?php endif; ?>
        <?= Html::a('Lihat Katalog', ['product/index'], ['class' => 'btn btn-primary']) ?>
    </div>

    <!-- Top Sale Section -->
    <h4 class="mb-3">Produk Terlaris</h4>
    <?php if (!empty($topSale)): ?>
        <div class="row mb-4">
            <?php foreach ($topSale as $product): ?>
                <div class="col-md-2 col-sm-4 col-6 mb-3">
                    <a href="<?= Url::to(['product/view', 'id' => $product->id]) ?>" class="text-decoration-none text-body">
                        <div class="card h-100">
                            <?php if (!empty($product->image)): ?>
                                <img src="<?= Yii::getAlias('@web/uploads/products/' . $product->image) ?>"
                                     class="card-img-top"
                                     alt="<?= Html::encode($product->name) ?>"
                                     style="height: 200px; object-fit: cover;">
                            <?php else: ?>
                                <div class="d-flex align-items-center justify-content-center bg-light"
                                     style="height: 200px;">
                                    <span class="text-muted small">Tidak ada gambar</span>
                                </div>
                            <?php endif; ?>
                            <div class="card-body p-2">
                                <p class="card-text small mb-1"><?= Html::encode($product->name) ?></p>
                                <p class="fw-bold small mb-0">Rp <?= number_format($product->price, 0, ',', '.') ?></p>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="text-muted mb-4">Belum ada data penjualan.</p>
    <?php endif; ?>

    <!-- Recently Purchased Section -->
    <h4 class="mb-3">Terakhir Dibeli</h4>
    <?php if (Yii::$app->user->isGuest): ?>
        <p class="text-muted">
            <?= Html::a('Login', ['site/login']) ?> untuk melihat riwayat pembelianmu.
        </p>
    <?php elseif (empty($recentProducts)): ?>
        <p class="text-muted">Kamu belum pernah melakukan pembelian.</p>
    <?php else: ?>
        <div class="row">
            <?php foreach ($recentProducts as $product): ?>
                <div class="col-md-2 col-sm-4 col-6 mb-3">
                    <a href="<?= Url::to(['product/view', 'id' => $product->id]) ?>" class="text-decoration-none text-body">
                        <div class="card h-100">
                            <?php if (!empty($product->image)): ?>
                                <img src="<?= Yii::getAlias('@web/uploads/products/' . $product->image) ?>"
                                     class="card-img-top"
                                     alt="<?= Html::encode($product->name) ?>"
                                     style="height: 200px; object-fit: cover;">
                            <?php else: ?>
                                <div class="d-flex align-items-center justify-content-center bg-light"
                                     style="height: 200px;">
                                    <span class="text-muted small">Tidak ada gambar</span>
                                </div>
                            <?php endif; ?>
                            <div class="card-body p-2">
                                <p class="card-text small mb-1"><?= Html::encode($product->name) ?></p>
                                <p class="fw-bold small mb-0">Rp <?= number_format($product->price, 0, ',', '.') ?></p>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</div>