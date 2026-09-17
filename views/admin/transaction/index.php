<?php

use yii\helpers\Html;
use yii\grid\GridView;

/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var string|null $code */
/** @var string|null $user_name */
/** @var string $period */
/** @var string|null $month */

$this->title = 'Riwayat Transaksi (Admin)';
?>
<h1><?= Html::encode($this->title) ?></h1>

<?= Html::beginForm(['admin/transaction/index'], 'get', ['class' => 'mb-3']) ?>
    <div class="row g-2 align-items-end">
        <div class="col-auto">
            <label class="form-label">Kode Transaksi</label>
            <?= Html::textInput('code', $code, ['class' => 'form-control', 'placeholder' => 'TRX-...']) ?>
        </div>
        <div class="col-auto">
            <label class="form-label">Username</label>
            <?= Html::textInput('user_name', $user_name, ['class' => 'form-control', 'placeholder' => 'Cari user...']) ?>
        </div>
        <div class="col-auto">
            <label class="form-label">Periode</label>
            <?= Html::dropDownList('period', $period, [
                'all'   => 'Semua',
                'today' => 'Hari Ini',
                'week'  => 'Minggu Ini',
                'month' => 'Pilih Bulan',
            ], [
                'class' => 'form-select',
                'id' => 'period-select',
                'onchange' => "document.getElementById('month-picker-wrapper').style.display = (this.value === 'month') ? 'block' : 'none';",
            ]) ?>
        </div>
        <div class="col-auto" id="month-picker-wrapper" style="display: <?= $period === 'month' ? 'block' : 'none' ?>;">
            <label class="form-label">Bulan</label>
            <?= Html::input('month', 'month', $month, ['class' => 'form-control']) ?>
        </div>
        <div class="col-auto">
            <?= Html::submitButton('Terapkan Filter', ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Reset', ['admin/transaction/index'], ['class' => 'btn btn-secondary']) ?>
        </div>
    </div>
<?= Html::endForm() ?>

<div class="table-responsive">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'code',
            'user_name',
            [
                'attribute' => 'created_at',
                'format' => ['date', 'php:d-m-Y H:i'],
            ],
            [
                'attribute' => 'total',
                'value' => function ($model) {
                    return 'Rp ' . number_format($model->total, 0, ',', '.');
                },
            ],
            [
                'attribute' => 'paid_amount',
                'label' => 'Dibayar',
                'value' => function ($model) {
                    return 'Rp ' . number_format($model->paid_amount, 0, ',', '.');
                },
            ],
            [
                'attribute' => 'change_amount',
                'label' => 'Kembalian',
                'value' => function ($model) {
                    return 'Rp ' . number_format($model->change_amount, 0, ',', '.');
                },
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
                'urlCreator' => function ($action, $model) {
                    return \yii\helpers\Url::to(['admin/transaction/view', 'id' => $model->id]);
                },
            ],
        ],
    ]) ?>
</div>