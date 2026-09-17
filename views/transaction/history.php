<?php

use yii\helpers\Html;
use yii\grid\GridView;

/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Riwayat Transaksi Saya';
?>
<h1><?= Html::encode($this->title) ?></h1>

<div class="table-responsive">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'code',
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
                    return \yii\helpers\Url::to(['transaction/success', 'id' => $model->id]);
                },
                'buttons' => [
                    'view' => function ($url) {
                        return Html::a('Detail', $url, ['class' => 'btn btn-sm btn-info']);
                    },
                ],
            ],
        ],
    ]) ?>
</div>