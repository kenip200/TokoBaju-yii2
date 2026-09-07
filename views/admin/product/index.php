<?php

use app\models\Product;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Products';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Product', ['create'], ['class' => 'btn btn-success']) ?>
    </p>


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            [
                'attribute' => 'category_id',
                'value' => 'category.name',
            ],
            'name',
            [
                'attribute' => 'description',
                'value' => function ($model) {
                    return $model->description
                        ? Html::encode(mb_substr($model->description, 0, 50)) . (mb_strlen($model->description) > 50 ? '...' : '')
                        : '-';
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'price',
                'value' => function ($model) {
                    return 'Rp ' . number_format($model->price, 0, ',', '.');
                },
            ],
            'stock',
            [
                'attribute' => 'image',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->image
                        ? Html::img(Yii::getAlias('@web/uploads/products/' . $model->image), ['style' => 'width:60px;height:60px;object-fit:cover'])
                        : '(no image)';
                },
            ],
            //'created_at',
            //'updated_at',
            //'description:ntext',
            //'image',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, Product $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>


</div>
