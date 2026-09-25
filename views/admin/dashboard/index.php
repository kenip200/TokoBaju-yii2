<?php
/**
 * Letakkan di: views/admin/dashboard/index.php
 *
 * @var $penjualanHarian array  ['tanggal', 'total_transaksi', 'total_penjualan']
 * @var $barangTerlaris  array  ['product_id', 'nama_produk', 'total_qty', 'total_pendapatan']
 * @var $grandTotal      float
 * @var $grandTransaksi  int
 * @var $tanggalAwal     string
 * @var $tanggalAkhir    string
 */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Dashboard Admin';
$this->params['breadcrumbs'][] = $this->title;

// data untuk chart.js
$chartLabels = array_column($penjualanHarian, 'tanggal');
$chartData   = array_column($penjualanHarian, 'total_penjualan');
?>

<div class="dashboard-admin">
    <h1><?= Html::encode($this->title) ?></h1>

    <!-- Filter tanggal -->
    <?php $form = \yii\bootstrap5\ActiveForm::begin([
        'method' => 'get',
        'action' => ['index'],
        'options' => ['class' => 'row g-2 align-items-end mb-4'],
    ]); ?>
        <div class="col-auto">
            <label class="form-label">Dari Tanggal</label>
            <input type="date" name="tanggal_awal" class="form-control"
                   value="<?= Html::encode($tanggalAwal) ?>">
        </div>
        <div class="col-auto">
            <label class="form-label">Sampai Tanggal</label>
            <input type="date" name="tanggal_akhir" class="form-control"
                   value="<?= Html::encode($tanggalAkhir) ?>">
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="<?= Url::to(['index']) ?>" class="btn btn-outline-secondary">Reset</a>
        </div>
    <?php \yii\bootstrap5\ActiveForm::end(); ?>

    <!-- Kartu ringkasan -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card bg-body-secondary h-100">
                <div class="card-body">
                    <div class="text-body-secondary small">Total Penjualan</div>
                    <div class="fs-3 fw-bold text-body-emphasis">
                        Rp <?= number_format($grandTotal, 0, ',', '.') ?>
                    </div>
                    <div class="text-body-secondary small">
                        <?= Html::encode($tanggalAwal) ?> s/d <?= Html::encode($tanggalAkhir) ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card bg-body-secondary h-100">
                <div class="card-body">
                    <div class="text-body-secondary small">Jumlah Transaksi</div>
                    <div class="fs-3 fw-bold text-body-emphasis"><?= $grandTransaksi ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card bg-body-secondary h-100">
                <div class="card-body">
                    <div class="text-body-secondary small">Rata-rata / Transaksi</div>
                    <div class="fs-3 fw-bold text-body-emphasis">
                        Rp <?= $grandTransaksi > 0 ? number_format($grandTotal / $grandTransaksi, 0, ',', '.') : 0 ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Chart + tabel penjualan harian -->
        <div class="col-lg-7 mb-4">
            <div class="card bg-body-secondary">
                <div class="card-body">
                    <h5 class="card-title text-body-emphasis">Total Penjualan Harian</h5>

                    <?php if (!empty($penjualanHarian)): ?>
                        <div style="position: relative; height: 200px;">
                            <canvas id="chartPenjualan"></canvas>
                        </div>
                    <?php endif; ?>

                    <div style="max-height: 300px; overflow-y: auto;">
                        <table class="table table-sm mt-3 mb-0">
                            <thead style="position: sticky; top: 0; z-index: 1;" class="bg-body-secondary">
                                <tr>
                                    <th>Tanggal</th>
                                    <th class="text-end">Transaksi</th>
                                    <th class="text-end">Total Penjualan</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if (empty($penjualanHarian)): ?>
                                <tr><td colspan="3" class="text-center text-body-secondary">Tidak ada data pada rentang ini</td></tr>
                            <?php else: ?>
                                <?php foreach ($penjualanHarian as $row): ?>
                                    <tr>
                                        <td><?= Html::encode($row['tanggal']) ?></td>
                                        <td class="text-end"><?= $row['total_transaksi'] ?></td>
                                        <td class="text-end">Rp <?= number_format($row['total_penjualan'], 0, ',', '.') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Barang terlaris -->
        <div class="col-lg-5 mb-4">
            <div class="card bg-body-secondary">
                <div class="card-body">
                    <h5 class="card-title text-body-emphasis">Barang Terlaris</h5>
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Produk</th>
                                <th class="text-end">Qty Terjual</th>
                                <th class="text-end">Pendapatan</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (empty($barangTerlaris)): ?>
                            <tr><td colspan="4" class="text-center text-body-secondary">Tidak ada data pada rentang ini</td></tr>
                        <?php else: ?>
                            <?php foreach ($barangTerlaris as $i => $row): ?>
                                <tr>
                                    <td><?= $i + 1 ?></td>
                                    <td><?= Html::encode($row['nama_produk']) ?></td>
                                    <td class="text-end"><?= $row['total_qty'] ?></td>
                                    <td class="text-end">Rp <?= number_format($row['total_pendapatan'], 0, ',', '.') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
if (!empty($penjualanHarian)):
    // pakai Chart.js dari CDN yang sudah umum dipakai; kalau sudah ada asset bundle Chart.js
    // di project, ganti bagian ini dengan register asset bundle-nya masing-masing.
    $this->registerJsFile('https://cdn.jsdelivr.net/npm/chart.js@4');
    $chartLabelsJson = json_encode($chartLabels);
    $chartDataJson   = json_encode($chartData);

    $js = <<<JS
const ctxPenjualan = document.getElementById('chartPenjualan');
if (ctxPenjualan) {
    new Chart(ctxPenjualan, {
        type: 'line',
        data: {
            labels: {$chartLabelsJson},
            datasets: [{
                label: 'Total Penjualan (Rp)',
                data: {$chartDataJson},
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } }
        }
    });
}
JS;
    $this->registerJs($js);
endif;
?>