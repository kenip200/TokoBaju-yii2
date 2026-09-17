<?php

use yii\helpers\Html;

/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var array $filters */

$periodLabel = [
    'all'   => 'Semua',
    'today' => 'Hari Ini',
    'week'  => 'Minggu Ini',
    'month' => 'Bulan ' . ($filters['month'] ?? ''),
];

$models = $dataProvider->getModels();
$grandTotal = 0;
?>
<h2>Riwayat Transaksi Global</h2>
<p class="subtitle">Dicetak pada <?= date('d-m-Y H:i') ?></p>

<div class="filter-info">
    <strong>Filter Aktif:</strong><br>
    Kode Transaksi: <?= Html::encode($filters['code'] ?: '-') ?><br>
    Username: <?= Html::encode($filters['user_name'] ?: '-') ?><br>
    Periode: <?= Html::encode($periodLabel[$filters['period']] ?? $filters['period']) ?><br>
    Total Data: <?= count($models) ?> transaksi
</div>

<table>
    <thead>
        <tr>
            <th style="width: 5%;">No</th>
            <th style="width: 15%;">Kode</th>
            <th style="width: 15%;">Username</th>
            <th style="width: 15%;">Tanggal</th>
            <th style="width: 15%;">Total</th>
            <th style="width: 15%;">Dibayar</th>
            <th style="width: 15%;">Kembalian</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($models)): ?>
            <tr>
                <td colspan="7" class="text-center">Tidak ada data transaksi.</td>
            </tr>
        <?php else: ?>
            <?php $no = 1; foreach ($models as $trx): ?>
                <?php $grandTotal += $trx->total; ?>
                <tr>
                    <td class="text-center"><?= $no++ ?></td>
                    <td><?= Html::encode($trx->code) ?></td>
                    <td><?= Html::encode($trx->user_name) ?></td>
                    <td class="text-center"><?= date('d-m-Y H:i', strtotime($trx->created_at)) ?></td>
                    <td class="text-right">Rp <?= number_format($trx->total, 0, ',', '.') ?></td>
                    <td class="text-right">Rp <?= number_format($trx->paid_amount, 0, ',', '.') ?></td>
                    <td class="text-right">Rp <?= number_format($trx->change_amount, 0, ',', '.') ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
    <?php if (!empty($models)): ?>
    <tfoot>
        <tr>
            <td colspan="4" class="text-right">Grand Total</td>
            <td class="text-right">Rp <?= number_format($grandTotal, 0, ',', '.') ?></td>
            <td colspan="2"></td>
        </tr>
    </tfoot>
    <?php endif; ?>
</table>